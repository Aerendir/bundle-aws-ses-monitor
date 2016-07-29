<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * (c) Adamo Aerendir Crespi.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;

/**
 * {@inheritdoc}
 */
class SnsSetupComplaintsTopicCommand extends SnsSetupCommandAbstract
{
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

    /**
     * {@inheritdoc}
     */
    public function getNotificationConfig()
    {
        return 'aws_ses_monitor.complaints';
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationKind()
    {
        return NotificationHandler::MESSAGE_TYPE_COMPLAINT;
    }
}
