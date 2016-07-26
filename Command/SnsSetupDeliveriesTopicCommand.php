<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;

/**
 * {@inheritdoc}
 */
class SnsSetupDeliveriesTopicCommand extends SnsSetupCommandAbstract
{
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

    /**
     * {@inheritdoc}
     */
    public function getNotificationConfig()
    {
        return 'aws_ses_monitor.deliveries';
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationKind()
    {
        return NotificationHandler::MESSAGE_TYPE_DELIVERY;
    }
}
