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

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;
use Symfony\Component\Routing\RouterInterface;

/**
 * Setups a topic to receive deliveries notifications from SNS.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class SnsSetupDeliveriesTopicCommand extends SnsSetupCommandAbstract
{
    /**
     * @param array            $deliveriesConfiguration
     * @param AwsClientFactory $awsClientFactory
     * @param RouterInterface  $router
     */
    public function __construct(array $deliveriesConfiguration, AwsClientFactory $awsClientFactory, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        parent::__construct($deliveriesConfiguration, NotificationHandler::MESSAGE_TYPE_DELIVERY, $awsClientFactory, $entityManager, $router);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription(
            'Registers SNS Topic, attaches it to chosen identities as delivery topic and subscribes endpoint to receive delivery notifications'
        );
        $this->setName('aws:ses:monitor:setup:deliveries-topic');
    }
}
