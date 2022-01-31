<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use function Safe\sprintf;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\Configuration;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\AwsDataProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\Console;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\IdentityGuesser;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Collects the current information from AWS SES and SNS.
 *
 * @internal
 */
final class Monitor
{
    /**
     * @var string
     */
    private const TRACK = 'track';

    /**
     * @var string
     */
    private const DKIM = 'dkim';

    /**
     * @var string
     */
    private const TOPIC = 'topic';

    /**
     * @var string
     */
    private const IDENTITIES = 'Identities';

    /**
     * @var string
     */
    private const SUBSCRIPTION_ARN = 'SubscriptionArn';

    /**
     * @var string
     */
    private const TOPIC_ARN = 'TopicArn';

    /** @var string $env */
    private $env;

    /** @var IdentitiesStore $configuredIdentities */
    private $configuredIdentities;

    /** @var array $liveData */
    private $liveData;

    /** @var AwsDataProcessor $awsDataProcessor */
    private $awsDataProcessor;

    /** @var Console $console */
    private $console;

    /** @var SesClient $sesClient */
    private $sesClient;

    /** @var SnsClient $snsClient */
    private $snsClient;

    /** @var IdentityGuesser $identityGuesser */
    private $identityGuesser;

    /** @var OutputInterface $sectionTitle */
    private $sectionTitle;

    /** @var OutputInterface $sectionBody */
    private $sectionBody;

    public function __construct(
        string $env,
        AwsDataProcessor $awsDataProcessor,
        Console $console,
        IdentitiesStore $configuredIdentities,
        IdentityGuesser $identityGuesser,
        SesClient $sesClient,
        SnsClient $snsClient
    ) {
        $this->env                  = $env;
        $this->awsDataProcessor     = $awsDataProcessor;
        $this->configuredIdentities = $configuredIdentities;
        $this->identityGuesser      = $identityGuesser;
        $this->console              = $console;
        $this->sesClient            = $sesClient;
        $this->snsClient            = $snsClient;
    }

    public function retrieve(OutputInterface $sectionTitle, OutputInterface $sectionBody, bool $withAccount = false): void
    {
        $this->sectionTitle = $sectionTitle;
        $this->sectionBody  = $sectionBody;

        if ($withAccount) {
            $this->fetchAccountData();
        }

        $this->fetchIdentitiesData();
        $this->fetchTopicsData();
        $this->fetchSubscriptionsData();

        $this->liveData = $this->awsDataProcessor->getProcessedData();

        $this->console->clear($this->sectionTitle);
        $this->console->clear($this->sectionBody);
    }

    /**
     * @param string|null $attributes
     *
     * @return mixed
     */
    public function getAccount(string $attributes = null)
    {
        $return = $this->liveData[AwsDataProcessor::ACCOUNT];

        if (null !== $attributes) {
            return $return[$attributes];
        }

        return $return;
    }

    /**
     * This finds an Identity.
     *
     * If passed an Email Identity and it is not configured, then the method
     * searches for the Domain Identity on which it depends.
     *
     * @param string      $identity
     * @param string|null $attributes
     *
     * @return array|bool|int|string
     */
    public function findConfiguredIdentity(string $identity, string $attributes = null)
    {
        $searchingIdentity = $identity;

        if (
            // If this identity isn't explicitly configured
            false === $this->configuredIdentities->identityExists($searchingIdentity) &&
            // and it is an Email Identity
            $this->getIdentityGuesser()->isEmailIdentity($searchingIdentity)
        ) {
            // Find its domain identity
            $parts = $this->getIdentityGuesser()->getEmailParts($searchingIdentity);

            $searchingIdentity = $parts[IdentityGuesser::DOMAIN];
        }

        // If the identity is not explicitly configured (the Email one or its Domain)
        if (false === $this->configuredIdentities->identityExists($searchingIdentity)) {
            $message = $this->getIdentityGuesser()->isEmailIdentity($identity)
                ? sprintf('The Email Identity "%s" nor its Domain identity are configured.', $identity)
                : sprintf('The Domain Identity "%s" is not configured.', $identity);

            throw new \InvalidArgumentException($message);
        }

        return $this->getConfiguredIdentity($searchingIdentity, $attributes);
    }

    /**
     * @param string|null $attributes
     *
     * @return array|bool|int|string
     */
    public function getConfiguredIdentity(string $identity, string $attributes = null)
    {
        return $this->configuredIdentities->getIdentity($identity, $attributes);
    }

    /**
     * @param bool $ignoreEnv if false, returns configured identities allowed on the current environment
     */
    public function getConfiguredIdentitiesList(bool $ignoreEnv = false): array
    {
        if ($ignoreEnv) {
            return [
                'allowed' => $this->configuredIdentities->getIdentitiesList(),
                'skipped' => [],
            ];
        }

        // We have to filter entities based on the environment
        $skipped = [];
        $allowed = [];
        foreach ($this->configuredIdentities->getIdentitiesList() as $identity) {
            // If we are in production and the identity is for production
            ('prod' === $this->env && $this->getIdentityGuesser()->isProductionIdentity($identity)) ||
            // Or if we are on dev (or in test envthe command) and
            // we passed --force (configures also production entities) or
            // we didn't pass the --force option BUT the identity is a test one
            (('dev' === $this->env || 'test' === $this->env) && ($ignoreEnv || $this->getIdentityGuesser()->isTestEmail($identity)))
                // Allow the identity
                ? $allowed[] = $identity
                // Skip the identity
                : $skipped[] = $identity;
        }

        return [
            'allowed' => $allowed,
            'skipped' => $skipped,
        ];
    }

    public function getLiveIdentity(string $identity, string $attributes = null): array
    {
        if (false === $this->liveIdentityExists($identity)) {
            return [];
        }

        $return = $this->liveData[AwsDataProcessor::IDENTITIES][$identity];

        if (null !== $attributes) {
            return $return[$attributes];
        }

        return $return;
    }

    public function getLiveIdentitiesList(): array
    {
        return \array_keys($this->liveData[AwsDataProcessor::IDENTITIES]);
    }

    public function getLiveTopic(string $normalizedTopicName): ?array
    {
        foreach ($this->getLiveTopicsList() as $topicArn) {
            if (false !== \strstr($topicArn, $normalizedTopicName)) {
                return $this->liveData[AwsDataProcessor::TOPICS][$topicArn];
            }
        }

        return null;
    }

    public function getLiveTopicsList(): array
    {
        if (false === isset($this->liveData[AwsDataProcessor::TOPICS])) {
            return [];
        }

        return \array_keys($this->liveData[AwsDataProcessor::TOPICS]);
    }

    public function bouncesTrackingIsEnabled(string $identity): bool
    {
        $config = $this->findConfiguredIdentity($identity, 'bounces');

        return $config[self::TRACK];
    }

    public function bouncesSendingIsForced(string $identity): bool
    {
        $config = $this->findConfiguredIdentity($identity, 'bounces');

        return $config['filter']['force_send'];
    }

    public function complaintsTrackingIsEnabled(string $identity): bool
    {
        $config = $this->findConfiguredIdentity($identity, 'complaints');

        return $config[self::TRACK];
    }

    public function complaintsSendingIsForced(string $identity): bool
    {
        $config = $this->findConfiguredIdentity($identity, 'complaints');

        return $config['filter']['force_send'];
    }

    /**
     * This can apply only to identities.
     */
    public function liveIdentityDkimIsEnabled(string $identity): bool
    {
        return $this->liveIdentityExists($identity) && $this->getLiveIdentity($identity, self::DKIM)['enabled'];
    }

    /**
     * This can apply only to identities.
     */
    public function liveIdentityDkimIsVerified(string $identity): bool
    {
        return
            $this->liveIdentityExists($identity) &&
            'Success' === $this->getLiveIdentity($identity, self::DKIM)['verification_status'];
    }

    public function liveIdentityExists(string $identity): bool
    {
        return isset($this->liveData[AwsDataProcessor::IDENTITIES][$identity]);
    }

    public function liveIdentityIsVerified(string $identity): bool
    {
        return
            $this->liveIdentityExists($identity) &&
            'Success' === $this->getLiveIdentity($identity, 'verification')['status'];
    }

    /**
     * @param string $type Type of notification: bounces, complaints or deliveries
     */
    public function liveNotificationsIncludeHeaders(string $identity, string $type): bool
    {
        return $this->getLiveIdentity($identity, 'notifications')[$type]['include_headers'];
    }

    public function liveTopicExists(string $normalizedTopicName): bool
    {
        foreach ($this->getLiveTopicsList() as $topicArn) {
            if (false !== \strstr($topicArn, $normalizedTopicName)) {
                return true;
            }
        }

        return false;
    }

    public function dkimEnabledIsInSync(string $identity): bool
    {
        return $this->getConfiguredIdentity($identity, self::DKIM) === ($this->getLiveIdentity($identity, self::DKIM)['enabled'] ?? null);
    }

    public function fromDomainIsInSync(string $identity): bool
    {
        return
            $this->getConfiguredIdentity($identity, 'on_mx_failure') === ($this->getLiveIdentity($identity, 'mail_from')['on_mx_failure'] ?? null) &&
            $this->getConfiguredIdentity($identity, 'from_domain')   === ($this->getLiveIdentity($identity, 'mail_from')[self::DOMAIN] ?? null);
    }

    public function fromDomainCanBeSynched(string $identity): bool
    {
        // If is a domain identity and is verified
        if (false === $this->identityGuesser->isEmailIdentity($identity) && $this->liveIdentityIsVerified($identity)) {
            // Then can be synched
            return true;
        }

        if ($this->identityGuesser->isEmailIdentity($identity)) {
            // This is an email identity: check its domain identity
            $parts    = $this->identityGuesser->getEmailParts($identity);
            $identity = $parts[self::DOMAIN];
        }

        return $this->liveIdentityIsVerified($identity);
    }

    /**
     * @param string $type Bounces, complaints or deliveries
     */
    public function requiresTopicConfiguration(string $identity, string $type): bool
    {
        $topicConfig = $this->getConfiguredIdentity($identity, $type);

        return $topicConfig[self::TRACK] && Configuration::USE_DOMAIN !== $topicConfig[self::TOPIC];
    }

    public function identityRequiresTopicSubscription(string $identity, string $notificationType): bool
    {
        $identityNotifications = $this->getLiveIdentity($identity, 'notifications');

        // If the notification SNS topic is not configured
        if (false === isset($identityNotifications[$notificationType]) || false === isset($identityNotifications[$notificationType][self::TOPIC])) {
            return true;
        }

        // If there are no subscriptions, then for sure we need to subscribe the identity
        if (empty($this->liveData[AwsDataProcessor::SUBSCRIPTIONS])) {
            return true;
        }

        // Check each subscription...
        foreach ($this->liveData[AwsDataProcessor::SUBSCRIPTIONS] as $subscription) {
            // If we find at least one subscription with the same topic arn
            if ($subscription['topic_arn'] === $identityNotifications[$notificationType][self::TOPIC]) {
                return false;
            }
        }

        // If all the previous conditions aren't met, return true as we didn't found a subscription that links the topic of the Identity with
        return true;
    }

    public function subscriptionEndpointIsInSynch(string $topicArn, string $currentEndpoint): bool
    {
        $subscription = $this->getTopicSubscriptions($topicArn);
        if (null === $subscription) {
            return false;
        }

        return $subscription['endpoint'] === $currentEndpoint;
    }

    public function getTopicSubscriptions(string $topicArn): ?array
    {
        if (false === isset($this->liveData[AwsDataProcessor::SUBSCRIPTIONS])) {
            return null;
        }

        foreach ($this->liveData[AwsDataProcessor::SUBSCRIPTIONS] as $subscription) {
            if ($subscription['topic_arn'] === $topicArn) {
                return $subscription;
            }
        }

        return null;
    }

    public function getIdentityGuesser(): IdentityGuesser
    {
        return $this->identityGuesser;
    }

    /**
     * Fetches the Account's data.
     */
    private function fetchAccountData(): void
    {
        $this->console->overwrite('Retrieving information of Account:', $this->sectionTitle);
        $this->console->overwrite('   Retrieving Account sending status...', $this->sectionBody);
        $accountSendingEnabled = $this->sesClient->getAccountSendingEnabled();
        $this->awsDataProcessor->processAccountSendingEnabled($accountSendingEnabled);

        $this->console->overwrite('   Retrieving Account send quota...', $this->sectionBody);
        $accountSendQuota = $this->sesClient->getSendQuota();
        $this->awsDataProcessor->processAccountSendQuota($accountSendQuota);

        $this->console->overwrite('   Retrieving Account send statistics...', $this->sectionBody);
        $accountSendStatistics = $this->sesClient->getSendStatistics();
        $this->awsDataProcessor->processAccountSendStatistics($accountSendStatistics);

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);
    }

    /**
     * Fetches the data of configured Identities.
     */
    private function fetchIdentitiesData(): void
    {
        $this->console->overwrite('Retrieving information of Identities:', $this->sectionTitle);
        $this->console->overwrite('   Retrieving DKIM attributes...', $this->sectionBody);
        $identitiesDkimAttributes = $this->sesClient->getIdentityDkimAttributes([self::IDENTITIES => $this->configuredIdentities->getIdentitiesList()]);
        $this->awsDataProcessor->processIdentitiesDkimAttributes($identitiesDkimAttributes);

        // This operation is throttled at one request per second and can only get custom MAIL FROM attributes for up to 100 identities at a time.
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitymailfromdomainattributes
        $this->console->overwrite('   Retrieving MAIL FROM domain attributes...', $this->sectionBody);
        $identitiesMailFromDomainAttributes = $this->sesClient->getIdentityMailFromDomainAttributes([self::IDENTITIES => $this->configuredIdentities->getIdentitiesList()]);
        $this->awsDataProcessor->processIdentitiesMailFromDomainAttributes($identitiesMailFromDomainAttributes);

        // This operation is throttled at one request per second and can only get custom MAIL FROM attributes for up to 100 identities at a time.
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitynotificationattributes
        $this->console->overwrite('   Retrieving notification attributes...', $this->sectionBody);
        $identityNotificationAttributes = $this->sesClient->getIdentityNotificationAttributes([self::IDENTITIES => $this->configuredIdentities->getIdentitiesList()]);
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
        $this->console->overwrite('   Retrieving verification attributes...', $this->sectionBody);
        $identityVerificationAttributes = $this->sesClient->getIdentityVerificationAttributes([self::IDENTITIES => $this->configuredIdentities->getIdentitiesList()]);
        $this->awsDataProcessor->processIdentitiesVerificationAttributes($identityVerificationAttributes);

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);
    }

    /**
     * Fetches data of subscriptions.
     */
    private function fetchSubscriptionsData(): void
    {
        // Returns a list of the requester's subscriptions. Each call returns a limited list of subscriptions, up to 100.
        // If there are more subscriptions, a NextToken is also returned. Use the NextToken parameter in a new
        // ListSubscriptions call to get further results.
        //
        // @todo This result has the token: use it to implement the cycling over paginated results
        //
        // This action is throttled at 30 transactions per second (TPS).
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#listsubscriptions
        $this->console->overwrite('Retrieving Subscriptions...', $this->sectionTitle);
        $subscriptions = $this->snsClient->listSubscriptions();
        $this->awsDataProcessor->processSubscriptions($subscriptions);

        foreach ($subscriptions->get('Subscriptions') as $subscription) {
            if ('PendingConfirmation' === $subscription[self::SUBSCRIPTION_ARN]) {
                continue;
            }

            $this->console->overwrite(sprintf('   Retrieving attributes for subscription <comment>%s</comment>', $subscription[self::SUBSCRIPTION_ARN]), $this->sectionBody);

            try {
                $subscriptionAttributes = $this->snsClient->getSubscriptionAttributes([self::SUBSCRIPTION_ARN => $subscription[self::SUBSCRIPTION_ARN]]);
                $this->awsDataProcessor->processSubscriptionAttributes($subscriptionAttributes);
            }
            // @codeCoverageIgnoreStart
            catch (\Throwable $throwable) {
                // Do nothing for the moment
                // This throws an error when the subscription doesn't exist.
                // The problem is that all the subscriptions are returned by the previous call to list subscriptions.
                // So, I have a call that returns me some subscriptions that don't exist. And this is a problem.
            }

            // @codeCoverageIgnoreEnd

            $this->console->clear($this->sectionBody);
        }

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);
    }

    /**
     * Fetches data of topics.
     */
    private function fetchTopicsData(): void
    {
        // Returns a list of the requester's topics. Each call returns a limited list of topics, up to 100.
        // If there are more topics, a NextToken is also returned. Use the NextToken parameter in a new
        // ListTopics call to get further results.
        //
        // This action is throttled at 30 transactions per second (TPS).
        // https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#listtopics
        $this->console->overwrite('Retrieving Topics...', $this->sectionTitle);
        $topics = $this->snsClient->listTopics();
        $this->awsDataProcessor->processTopics($topics);

        foreach ($topics->get('Topics') as $topic) {
            $this->console->overwrite(sprintf('   Retrieving attributes for topic <comment>%s</comment>', $topic[self::TOPIC_ARN]), $this->sectionBody);
            //try {
            $topicAttributes = $this->snsClient->getTopicAttributes([self::TOPIC_ARN => $topic[self::TOPIC_ARN]]);
            $this->awsDataProcessor->processTopicAttributes($topicAttributes);
            //} catch (\Throwable $e) {
            // Do nothing for the moment
            // This throws an error when the subscription doesn't exist.
            // The problem is that all the subscriptions are returned by the previous call to list subscriptions.
            // So, I have a call that returns me some subscriptions that don't exist. And this is a problem.
            //}
        }

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);
    }
}
