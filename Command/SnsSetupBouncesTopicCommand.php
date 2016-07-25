<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

/**
 * {@inheritdoc}
 */
class SnsSetupBouncesTopicCommand extends SnsSetupCommandAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription(
            'Registers SNS Topic, attaches it to chosen identities as bounce topic and subscribes endpoint to receive bounce notifications'
        );
        $this->setName('aws:ses:monitor:setup:bounces-topic');
    }

    /**
     * {@inheritdoc}
     */
    public function getTopicName()
    {
        return 'aws_ses_monitor.bounces';
    }

    /**
     * {@inheritdoc}
     */
    public function getTopicKind()
    {
        return 'Bounce';
    }
}
