<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\AwsDataProcessor;
use SerendipityHQ\Bundle\ConsoleStyles\Console\Formatter\SerendipityHQOutputFormatter;
use SerendipityHQ\Bundle\ConsoleStyles\Console\Style\SerendipityHQStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract class to perform common command tasks.
 * {@inheritdoc}
 */
class DebugCommand extends Command
{
    const THICK = "<fg=green>\xE2\x9C\x94</>";
    const CROSS = "<fg=red>\xE2\x9C\x96</>";

    /** @var AwsDataProcessor $awsDataProcessor */
    private $awsDataProcessor;

    /** @var SesClient $sesClient */
    private $sesClient;

    /** @var SnsClient $snsClient */
    private $snsClient;

    /**
     * @param AwsDataProcessor $awsDataProcessor
     * @param SesClient        $sesClient
     * @param SnsClient        $snsClient
     */
    public function __construct(AwsDataProcessor $awsDataProcessor, SesClient $sesClient, SnsClient $snsClient)
    {
        $this->awsDataProcessor = $awsDataProcessor;
        $this->sesClient        = $sesClient;
        $this->snsClient        = $snsClient;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Debugs the aws ses configuration helping discovering errors and wrong settings.')
             ->setName('aws:ses:debug');
    }

    /**
     * {@inheritdoc}
     *
     * @param ConsoleOutput&OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create the Input/Output writer
        $ioWriter = new SerendipityHQStyle($input, $output);
        $ioWriter->setFormatter(new SerendipityHQOutputFormatter(true));

        $ioWriter->title('Aws SES Monitor Debug');

        $section = $this->createSection($output);

        $this->fetchAccountData($section);
        $this->fetchIdentitiesData($section);
        $this->fetchSubscriptionsData($section);
        $this->fetchTopicsData($section);

        $validationResults                               = [];
        $data                                            = $this->awsDataProcessor->getProcessedData();
        $validationResults[AwsDataProcessor::ACCOUNT]    = $this->validateAccountData($data, $section);
        $validationResults[AwsDataProcessor::IDENTITIES] = $this->validateIdentitiesData($data, $section);

        $section->overwrite('Preparing data');
        $this->showData($validationResults, $section);
    }

    /**
     * This method is required to make the static analysis pass.
     *
     * The problem is on Symfony's code base side, as the ConsoleOutput object has
     * the ::section() method, but the OutputInterface doesn't.
     *
     * @see https://github.com/symfony/symfony/issues/27998
     *
     * @param ConsoleOutput|OutputInterface $output
     *
     * @return ConsoleSectionOutput
     */
    private function createSection($output): ConsoleSectionOutput
    {
        if ($output instanceof ConsoleOutput) {
            return $output->section();
        }

        throw new \InvalidArgumentException('This will never happen. This static error in Symfony code is very bothersome.');
    }

    /**
     * @param ConsoleSectionOutput $section
     */
    private function fetchAccountData(ConsoleSectionOutput $section): void
    {
        $section->overwrite('Retrieving Account sending status');
        $accountSendingEnabled = $this->sesClient->getAccountSendingEnabled();
        $this->awsDataProcessor->processAccountSendingEnabled($accountSendingEnabled);

        $section->overwrite('Retrieving Account send quota');
        $accountSendQuota = $this->sesClient->getSendQuota();
        $this->awsDataProcessor->processAccountSendQuota($accountSendQuota);

        $section->overwrite('Retrieving Account send statistics');
        $accountSendStatistics = $this->sesClient->getSendStatistics();
        $this->awsDataProcessor->processAccountSendStatistics($accountSendStatistics);
    }

    /**
     * @param ConsoleSectionOutput $section
     */
    private function fetchIdentitiesData(ConsoleSectionOutput $section): void
    {
        $section->overwrite('Retrieving Identities');
        $identitiesList = $this->sesClient->listIdentities();
        $this->awsDataProcessor->processIdentities($identitiesList);

        $section->overwrite('Retrieving DKIM attributes');
        $identitiesDkimAttributes = $this->sesClient->getIdentityDkimAttributes(['Identities' => $identitiesList->get('Identities')]);
        $this->awsDataProcessor->processIdentitiesDkimAttributes($identitiesDkimAttributes);

        // This operation is throttled at one request per second and can only get custom MAIL FROM attributes for up to 100 identities at a time.
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitymailfromdomainattributes
        $section->overwrite('Retrieving MAIL FROM domain attributes');
        $identitiesMailFromDomainAttributes = $this->sesClient->getIdentityMailFromDomainAttributes(['Identities' => $identitiesList->get('Identities')]);
        $this->awsDataProcessor->processIdentitiesMailFromDomainAttributes($identitiesMailFromDomainAttributes);

        // This operation is throttled at one request per second and can only get custom MAIL FROM attributes for up to 100 identities at a time.
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitynotificationattributes
        $section->overwrite('Retrieving notification attributes');
        $identityNotificationAttributes = $this->sesClient->getIdentityNotificationAttributes(['Identities' => $identitiesList->get('Identities')]);
        $this->awsDataProcessor->processIdentitiesNotificationAttributes($identityNotificationAttributes);

        // Given a list of identities (email addresses and/or domains), returns the verification
        // status and (for domain identities) the verification token for each identity.
        //
        // The verification status of an email address is "Pending" until the email address owner clicks the
        // link within the verification email that Amazon SES sent to that address. If the email address
        // owner clicks the link within 24 hours, the verification status of the email address changes to "Success".
        // If the link is not clicked within 24 hours, the verification status changes to "Failed." In that case,
        // if you still want to verify the email address, you must restart the verification process from the beginning.
        //
        // For domain identities, the domain's verification status is "Pending" as Amazon SES searches for the required
        // TXT record in the DNS settings of the domain. When Amazon SES detects the record, the domain's verification
        // status changes to "Success". If Amazon SES is unable to detect the record within 72 hours, the domain's
        // verification status changes to "Failed." In that case, if you still want to verify the domain, you must
        // restart the verification process from the beginning.
        //
        // This operation is throttled at one request per second and can only get verification attributes for up to 100 identities at a time.
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentityverificationattributes
        $section->overwrite('Retrieving verification attributes');
        $identityVerificationAttributes = $this->sesClient->getIdentityVerificationAttributes(['Identities' => $identitiesList->get('Identities')]);
        $this->awsDataProcessor->processIdentitiesVerificationAttributes($identityVerificationAttributes);
    }

    /**
     * @param ConsoleSectionOutput $section
     */
    private function fetchSubscriptionsData(ConsoleSectionOutput $section): void
    {
        // Returns a list of the requester's subscriptions. Each call returns a limited list of subscriptions, up to 100.
        // If there are more subscriptions, a NextToken is also returned. Use the NextToken parameter in a new
        // ListSubscriptions call to get further results.
        //
        // @todo This result has the token: use it to implement the cycling over paginated results
        //
        // This action is throttled at 30 transactions per second (TPS).
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#listsubscriptions
        $section->overwrite('Retrieving Subscriptions');
        $subscriptions = $this->snsClient->listSubscriptions();
        $this->awsDataProcessor->processSubscriptions($subscriptions);

        foreach ($subscriptions->get('Subscriptions') as $subscription) {
            $section->overwrite(sprintf('Retrieving attributes for subscription <comment>%s</comment>', $subscription['SubscriptionArn']));
            try {
                $subscriptionAttributes = $this->snsClient->getSubscriptionAttributes(['SubscriptionArn' => $subscription['SubscriptionArn']]);
                $this->awsDataProcessor->processSubscriptionAttributes($subscriptionAttributes);
            } catch (\Throwable $e) {
                // Do nothing for the moment
                // This throws an error when the subscription doesn't exist.
                // The problem is that all the subscriptions are returned by the previous call to list subscriptions.
                // So, I have a call that returns me some subscriptions that don't exist. And this is a problem.
            }
        }
    }

    /**
     * @param ConsoleSectionOutput $section
     */
    private function fetchTopicsData(ConsoleSectionOutput $section): void
    {
        $section->overwrite('Debugging Topics');

        // Returns a list of the requester's topics. Each call returns a limited list of topics, up to 100.
        // If there are more topics, a NextToken is also returned. Use the NextToken parameter in a new
        // ListTopics call to get further results.
        //
        // This action is throttled at 30 transactions per second (TPS).
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#listtopics
        $section->overwrite('Retrieving Topics');
        $topics = $this->snsClient->listTopics();
        $this->awsDataProcessor->processTopics($topics);

        foreach ($topics->get('Topics') as $topic) {
            $section->overwrite(sprintf('Retrieving attributes for topic <comment>%s</comment>', $topic['TopicArn']));
            //try {
            $topicAttributes = $this->snsClient->getTopicAttributes(['TopicArn' => $topic['TopicArn']]);
            $this->awsDataProcessor->processTopicAttributes($topicAttributes);
            //} catch (\Throwable $e) {
                // Do nothing for the moment
                // This throws an error when the subscription doesn't exist.
                // The problem is that all the subscriptions are returned by the previous call to list subscriptions.
                // So, I have a call that returns me some subscriptions that don't exist. And this is a problem.
            //}
        }
    }

    /**
     * @param array                $data
     * @param ConsoleSectionOutput $section
     *
     * @return array
     */
    private function validateAccountData(array $data, ConsoleSectionOutput $section): array
    {
        $results = [];

        $section->overwrite('Validating Account');

        // Can the account send emails?
        $results[] = ['   Account enabled for sending', $data[AwsDataProcessor::ACCOUNT]['enabled'] ? self::THICK : self::CROSS];

        // Is the quota exceeded?
        $results[] = [
            '   Quota still available',
            $data[AwsDataProcessor::ACCOUNT]['quota']['sent_last_24_hours'] <= $data[AwsDataProcessor::ACCOUNT]['quota']['max_24_hour_send']
                ? self::THICK
                : self::CROSS,
        ];

        return $results;
    }

    /**
     * @param array                $data
     * @param ConsoleSectionOutput $section
     *
     * @return array
     */
    private function validateIdentitiesData(array $data, ConsoleSectionOutput $section): array
    {
        $results = [];

        foreach ($data[AwsDataProcessor::IDENTITIES] as $identity => $details) {
            $section->overwrite(sprintf('Validating identity <comment>%s</comment>', $identity));

            // Is identity verified?
            $results[$identity][] = ['   Verified (enabled for sending)', 'Success' === $details['verification']['status'] ? self::THICK : self::CROSS];

            // Is DKIM enabled?
            $results[$identity][] = ['   DKIM enabled', $details['dkim']['enabled'] ? self::THICK : self::CROSS];

            // Is DKIM verified?
            $results[$identity][] = ['   DKIM verified', 'Success' === $details['dkim']['verification_status'] ? self::THICK : self::CROSS];

            // Notifications include headers?
            $results[$identity][] = ['   Bounces notifications include headers', $details['notifications']['bounces']['include_headers'] ? self::THICK : self::CROSS];
            $results[$identity][] = ['   Complaints notifications include headers', $details['notifications']['complaints']['include_headers'] ? self::THICK : self::CROSS];
            $results[$identity][] = ['   Deliveries notifications include headers', $details['notifications']['deliveries']['include_headers'] ? self::THICK : self::CROSS];
        }

        return $results;
    }

    /**
     * @param array                $validationResults
     * @param ConsoleSectionOutput $section
     */
    private function showData(array $validationResults, ConsoleSectionOutput $section): void
    {
        $table = new Table($section);
        $table->setHeaders([
            [new TableCell('Results', ['colspan' => 2])],
        ]);

        $section->overwrite('Processing Account results');
        $table->addRow([new TableCell('<success>ACCOUNT</success>', ['colspan' => 2])]);
        foreach ($validationResults[AwsDataProcessor::ACCOUNT] as $result) {
            $table->addRow($result);
        }

        $section->overwrite('Processing Identities results');
        $table->addRow([new TableCell('<success>IDENTITIES</success>', ['colspan' => 2])]);
        foreach ($validationResults[AwsDataProcessor::IDENTITIES] as $identity => $results) {
            $table->addRow([new TableCell($identity, ['colspan' => 2])]);
            foreach ($results as $result) {
                $table->addRow($result);
            }
        }

        // Finally render the table with results
        $section->clear();
        $table->render();
    }
}
