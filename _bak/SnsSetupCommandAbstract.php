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
use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Factory\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * Abstract class to perform common command tasks.
 * {@inheritdoc}
 */
abstract class SnsSetupCommandAbstract extends Command
{
    const THICK = "<fg=green>\xE2\x9C\x94</>";
    const CROSS = "<fg=magenta>\xE2\x9C\x96</>";

    /** @var array $topicConfig */
    private $topicConfig;

    /** @var string $kind */
    private $notificationType;

    /** @var AwsClientFactory $awsClientFactory */
    private $awsClientFactory;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var RouterInterface $router */
    private $router;

    /** @var RequestContext $requestContext */
    private $requestContext;

    /** @var SesClient $sesClient */
    private $sesClient;

    /** @var SnsClient $snsClient */
    private $snsClient;

    /** @var string $topicArn */
    private $topicArn;

    /**
     * @param array                  $configuration
     * @param string                 $notificationType
     * @param AwsClientFactory       $awsClientFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     */
    public function __construct(array $configuration, string $notificationType, AwsClientFactory $awsClientFactory, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->topicConfig      = $configuration['topic'];
        $this->notificationType = $notificationType;
        $this->awsClientFactory = $awsClientFactory;
        $this->entityManager    = $entityManager;
        $this->router           = $router;
        $this->requestContext   = $router->getContext();
        $this->sesClient        = $this->awsClientFactory->getSesClient();
        $this->snsClient        = $this->awsClientFactory->getSnsClient();

        $this->requestContext->setHost($this->topicConfig['endpoint']['host']);
        $this->requestContext->setScheme($this->topicConfig['endpoint']['scheme']);

        parent::__construct();
    }

    /**
     * @return SesClient
     */
    public function getSesClient(): SesClient
    {
        return $this->sesClient;
    }

    /**
     * @return SnsClient
     */
    public function getSnsClient(): SnsClient
    {
        return $this->snsClient;
    }

    /**
     * Creates the questions to show to the developer during setup.
     *
     * The developer has to chose to which identity the created SNS has to be hooked.
     *
     * @return ChoiceQuestion
     */
    public function createIdentitiesQuestion(): ChoiceQuestion
    {
        $response   = $this->getSesClient()->listIdentities();
        $identities = $response->get('Identities');
        $question   = new ChoiceQuestion(
            'Please select identities to hook to: (comma separated numbers, default: all)',
            $identities,
            implode(',', range(0, count($identities) - 1, 1))
        );
        $question->setMultiselect(true);

        return $question;
    }

    /**
     * Creates and persists a topic.
     *
     * @param OutputInterface $output
     *
     * @return bool If the topic was created or not
     */
    public function createSnsTopic(OutputInterface $output): bool
    {
        if ('not_set' === $this->topicConfig['name']) {
            switch ($this->notificationType) {
                case SnsTypes::MESSAGE_TYPE_BOUNCE:
                    $topicKind = 'bounces';
                    break;
                case SnsTypes::MESSAGE_TYPE_COMPLAINT:
                    $topicKind = 'complaints';
                    break;
                case SnsTypes::MESSAGE_TYPE_DELIVERY:
                    $topicKind = 'deliveries';
                    break;
                default:
                    throw new \RuntimeException('The MESSAGE_TYPE given is not recognized. Review the code of commands that inherit from SnsSetupCommandAbstract.');
            }

            $output->writeln(sprintf('<error>You have to set a name for the creating topic. Specify it in "shq_aws_ses_monitor.%s.name".</error>', $topicKind));

            return false;
        }

        // create SNS topic
        $topic          = ['Name' => $this->topicConfig['name']];
        $response       = $this->getSnsClient()->createTopic($topic);
        $this->topicArn = $response->get('TopicArn');

        $topic = new Topic($this->topicArn);

        $this->entityManager->persist($topic);

        return true;
    }

    /**
     * Sets the chosen identity in the SesClient.
     *
     * @param string $identity
     * @param string $notificationType The type of notification
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitynotificationtopic
     */
    public function setIdentityInSesClient(string $identity, string $notificationType): void
    {
        $this->getSesClient()->setIdentityNotificationTopic(
            [
                'Identity'         => $identity,
                'NotificationType' => $notificationType,
                'SnsTopic'         => $this->topicArn,
            ]
        );
    }

    /**
     * @return array
     */
    public function buildSubscribeArray(): array
    {
        return [
            'TopicArn'   => $this->topicArn,
            'Protocol'   => $this->topicConfig['endpoint']['scheme'],
            'Endpoint'   => $this->router->generate(
                $this->topicConfig['endpoint']['route_name'],
                [],
                RouterInterface::ABSOLUTE_URL
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Show to developer the selction of identities
        $selectedIdentities = $this->getHelper('question')->ask($input, $output, $this->createIdentitiesQuestion());

        // Create and persist the topic
        $output->writeln(sprintf('Creating the topic <comment>%s</comment>', $this->topicConfig['name']));
        if (false === $this->createSnsTopic($output)) {
            $output->writeln(sprintf('<error>Topic <comment>%s</comment> was not created.</error>', $this->topicConfig['name']));

            return 1;
        }

        $output->writeln(sprintf('<fg=green>Topic <comment>%s</comment> created: <comment>%s</comment></>', $this->topicConfig['name'], $this->topicArn));
        $output->writeln('');

        // Subscribe selected SES identities to SNS topic
        $output->writeln(sprintf('Subscribing <comment>"%s"</comment> topic to identities:', $this->topicConfig['name']));
        foreach ($selectedIdentities as $identity) {
            $output->write('- ' . $identity . ' ... ');
            $this->setIdentityInSesClient($identity, $this->notificationType);
            $output->writeln(self::THICK);
        }
        $output->writeln('');

        // Set the SNS to the app's endpoint
        $output->writeln('Subscribing the App\'s Endpoint to the Topic:');
        $subscribe = $this->buildSubscribeArray();

        try {
            $response = $this->getSnsClient()->subscribe($subscribe);
        } catch (SnsException $e) {
            $output->writeln(sprintf('<error>%s Error %s: %s</error>', self::CROSS, $e->getAwsErrorCode(), $e->getAwsErrorMessage()));

            return 1;
        }

        $this->entityManager->flush();

        $output->writeln(sprintf('<fg=green>%s Endpoint <comment>%s</comment> added to Topic <comment>%s</comment></>', self::THICK, $subscribe['Endpoint'], $subscribe['TopicArn']));
        $output->writeln(sprintf('Subscription status: <comment>%s</comment>', $response->get('SubscriptionArn')));

        return 0;
    }
}
