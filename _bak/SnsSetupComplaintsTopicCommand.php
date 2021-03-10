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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Factory\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\Routing\RouterInterface;

/**
 * Setups a topic to receive complaints notifications from SNS.
 * {@inheritdoc}
 */
class SnsSetupComplaintsTopicCommand extends SnsSetupCommandAbstract
{
    /**
     * @param array                  $complaintsConfig
     * @param AwsClientFactory       $awsClientFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     */
    public function __construct(array $complaintsConfig, AwsClientFactory $awsClientFactory, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        parent::__construct($complaintsConfig, SnsTypes::MESSAGE_TYPE_COMPLAINT, $awsClientFactory, $entityManager, $router);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription(
            'Registers SNS Topic, attaches it to chosen identities as complaint topic and subscribes endpoint to receive complaint notifications'
        );
        $this->setName('aws:ses:monitor:setup:complaints-topic');
    }
}
