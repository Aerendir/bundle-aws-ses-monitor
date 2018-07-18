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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * Abstract class to perform common command tasks.
 * {@inheritdoc}
 */
class SubscribeCommand extends Command
{
    const THICK = "<fg=green>\xE2\x9C\x94</>";
    const CROSS = "<fg=magenta>\xE2\x9C\x96</>";

    /** @var array $bouncesConfig */
    private $bouncesConfig;

    /** @var array $complaintsConfig */
    private $complaintsConfig;

    /** @var array $deliveriesConfig */
    private $deliveriesConfig;

    /** @var array $endpointConfig */
    private $endpointConfig;

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
     * @param array                  $bouncesConfig
     * @param array                  $complaintsConfig
     * @param array                  $deliveriesConfig
     * @param array                  $endpointConfig
     * @param AwsClientFactory       $awsClientFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     */
    public function __construct(
        array $bouncesConfig,
        array $complaintsConfig,
        array $deliveriesConfig,
        array $endpointConfig,
        AwsClientFactory $awsClientFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->bouncesConfig    = $bouncesConfig;
        $this->complaintsConfig = $complaintsConfig;
        $this->deliveriesConfig = $deliveriesConfig;
        $this->endpointConfig   = $endpointConfig;
        $this->entityManager    = $entityManager;
        $this->router           = $router;
        $this->requestContext   = $router->getContext();
        $this->sesClient        = $awsClientFactory->getSesClient();
        $this->snsClient        = $awsClientFactory->getSnsClient();

        $this->requestContext->setHost($this->endpointConfig['host']);
        $this->requestContext->setScheme($this->endpointConfig['scheme']);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Subscribes the application to topics to get notifications from AWS SES, creating them if they don\'t still exist.')
             ->setName('aws:ses:monitor:subscribe')
            ->addOption('bounces', 'b', InputOption::VALUE_NONE, 'Subscribe to bounces notifications.')
            ->addOption('complaints', 'c', InputOption::VALUE_NONE, 'Subscribe to bounces notifications.')
            ->addOption('deliveries', 'd', InputOption::VALUE_NONE, 'Subscribe to bounces notifications.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = 0;
        if ($input->getOption('bounces')) {
            if (0 !== $this->subscribe(SnsTypes::MESSAGE_TYPE_BOUNCE, $input, $output)) {
                $status = 1;
            }
        }

        if ($input->getOption('complaints')) {
            if (0 !== $this->subscribe(SnsTypes::MESSAGE_TYPE_COMPLAINT, $input, $output)) {
                $status = 1;
            }
        }

        if ($input->getOption('deliveries')) {
            if (0 !== $this->subscribe(SnsTypes::MESSAGE_TYPE_DELIVERY, $input, $output)) {
                $status = 1;
            }
        }

        return $status;
    }

    /**
     * @param string          $messageType
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function subscribe(string $messageType, InputInterface $input, OutputInterface $output)
    {
        // Show to developer the selction of identities
        $selectedIdentities = $this->getHelper('question')->ask($input, $output, $this->createIdentitiesQuestion());

        // Create and persist the topic
        $output->writeln(sprintf('Creating the topic <comment>%s</comment>', $messageType));

        switch ($messageType) {
            case SnsTypes::MESSAGE_TYPE_BOUNCE:
                $topicName = $this->bouncesConfig['topic'];
                break;
            case SnsTypes::MESSAGE_TYPE_COMPLAINT:
                $topicName = $this->complaintsConfig['topic'];
                break;
            case SnsTypes::MESSAGE_TYPE_DELIVERY:
                $topicName = $this->deliveriesConfig['topic'];
                break;
            default:
                throw new \RuntimeException(sprintf('The MESSAGE_TYPE "%s" given is not recognized.', $messageType));
        }

        try {
            $topic = $this->createSnsTopic($topicName);
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>Topic <comment>%s</comment> was not created. Error: %s</error>', $messageType, $e->getMessage()));

            return 1;
        }

        $output->writeln(sprintf('<fg=green>Topic <comment>%s</comment> created: <comment>%s</comment></>', $messageType, $topic->getTopicArn()));
        $output->writeln('');

        // Subscribe selected SES identities to SNS topic
        $output->writeln(sprintf('Subscribing <comment>"%s"</comment> topic to identities:', $topicName));
        foreach ($selectedIdentities as $identity) {
            $output->write('- ' . $identity . ' ... ');
            $this->setIdentityInSesClient($identity, $messageType, $topic->getTopicArn());
            $output->writeln(self::THICK);
        }
        $output->writeln('');

        // Set the SNS to the app's endpoint
        $output->writeln('Subscribing the App\'s Endpoint to the Topic:');
        $subscribe = $this->buildSubscribeArray($topic->getTopicArn());

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

    /**
     * @return SesClient
     */
    private function getSesClient(): SesClient
    {
        return $this->sesClient;
    }

    /**
     * @return SnsClient
     */
    private function getSnsClient(): SnsClient
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
    private function createIdentitiesQuestion(): ChoiceQuestion
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
     * @param string $topicName
     *
     * @return Topic
     */
    private function createSnsTopic(string $topicName): Topic
    {
        // create SNS topic
        $topic          = ['Name' => $topicName];
        $response       = $this->getSnsClient()->createTopic($topic);
        $this->topicArn = $response->get('TopicArn');

        $topic = new Topic($this->topicArn);

        $this->entityManager->persist($topic);

        return $topic;
    }

    /**
     * Sets the chosen identity in the SesClient.
     *
     * @param string $identity
     * @param string $notificationType The type of notification
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitynotificationtopic
     */
    private function setIdentityInSesClient(string $identity, string $notificationType, string $topicArn): void
    {
        $this->getSesClient()->setIdentityNotificationTopic(
            [
                'Identity'         => $identity,
                'NotificationType' => $notificationType,
                'SnsTopic'         => $topicArn,
            ]
        );
    }

    /**
     * @param string $topicArn
     *
     * @return array
     */
    private function buildSubscribeArray(string $topicArn): array
    {
        return [
            'TopicArn'   => $topicArn,
            'Protocol'   => $this->endpointConfig['scheme'],
            'Endpoint'   => $this->router->generate('_shq_aws_ses_monitor_endpoint', [], RouterInterface::ABSOLUTE_URL),
        ];
    }
}
