<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

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
            'Registers SNS Topic, attaches it to chosen identities as bounce topic and subscribes endpoint to receive bounce notifications'
        );
        $this->setName('aws:ses:monitor:setup:complaints-topic');
    }

    /**
     * {@inheritdoc}
     */
    public function getTopicName()
    {
        return 'aws_ses_monitor.complaints';
    }

    /**
     * {@inheritdoc}
     */
    public function getTopicKind()
    {
        return 'Complaint';
    }
}
