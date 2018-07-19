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

use Aws\Sns\Exception\SnsException;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\SubscribeHelper;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SesManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SnsManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract class to perform common command tasks.
 * {@inheritdoc}
 */
class ConfigureCommand extends Command
{
    const THICK = "<fg=green>\xE2\x9C\x94</>";
    const CROSS = "<fg=magenta>\xE2\x9C\x96</>";

    /** @var array $bouncesConfig */
    private $bouncesConfig;

    /** @var array $complaintsConfig */
    private $complaintsConfig;

    /** @var array $deliveriesConfig */
    private $deliveriesConfig;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var SubscribeHelper $subscribeHelper */
    private $subscribeHelper;

    /** @var SesManager $sesManager */
    private $sesManager;

    /** @var SnsManager $snsManager */
    private $snsManager;

    /**
     * @param array                  $bouncesConfig
     * @param array                  $complaintsConfig
     * @param array                  $deliveriesConfig
     * @param EntityManagerInterface $entityManager
     * @param SesManager             $sesManager
     * @param SnsManager             $snsManager
     * @param SubscribeHelper        $subscribeHelper
     */
    public function __construct(
        array $bouncesConfig,
        array $complaintsConfig,
        array $deliveriesConfig,
        EntityManagerInterface $entityManager,
        SesManager $sesManager,
        SnsManager $snsManager,
        SubscribeHelper $subscribeHelper
    ) {
        $this->bouncesConfig    = $bouncesConfig;
        $this->complaintsConfig = $complaintsConfig;
        $this->deliveriesConfig = $deliveriesConfig;
        $this->entityManager    = $entityManager;
        $this->sesManager       = $sesManager;
        $this->snsManager       = $snsManager;
        $this->subscribeHelper  = $subscribeHelper;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Subscribes the application to topics to get notifications from AWS SES, creating them if they don\'t still exist.')
             ->setName('aws:ses:configure')
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
        $selectedIdentities = $this->getHelper('question')->ask($input, $output, $this->subscribeHelper->createIdentityQuestion());

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
            $topic = $this->snsManager->createTopic($topicName);
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>Topic <comment>%s</comment> was not created. Error: %s</error>', $messageType, $e->getMessage()));

            return 1;
        }

        $this->entityManager->persist($topic);

        $output->writeln(sprintf('<fg=green>Topic <comment>%s</comment> created: <comment>%s</comment></>', $messageType, $topic->getTopicArn()));
        $output->writeln('');

        // Subscribe selected SES identities to SNS topic
        $output->writeln(sprintf('Setting topic <comment>"%s"</comment> in identities:', $topicName));
        foreach ($selectedIdentities as $identity) {
            $output->write('- ' . $identity . ' ... ');
            $this->sesManager->setTopic($identity, $messageType, $topic->getTopicArn());
            $output->writeln(self::THICK);

            // Wait 1 second to avoid throttling errors
            usleep(1000000);
        }
        $output->writeln('');

        // Set the SNS to the app's endpoint
        $output->writeln('Subscribing the App\'s Endpoint to the Topic:');

        try {
            $subscriptionArn = $this->snsManager->setEndpoint($topic);
        } catch (SnsException $e) {
            $output->writeln(sprintf('<error>%s Error %s: %s</error>', self::CROSS, $e->getAwsErrorCode(), $e->getAwsErrorMessage()));

            return 1;
        }

        $this->entityManager->flush();

        $output->writeln(sprintf('<fg=green>%s Endpoint <comment>%s</comment> added to Topic <comment>%s</comment></>', self::THICK, $this->snsManager->getEndpointUrl(), $topic->getTopicArn()));
        $output->writeln(sprintf('Subscription status: <comment>%s</comment>', $subscriptionArn));

        return 0;
    }
}
