<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;

/**
 * Tests the Topic entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class TopicTest extends \PHPUnit_Framework_TestCase
{
    public function testTopic()
    {
        $test = [
            'topicArn' => 'test@example.com',
            'token'    => $this->createMock(MailMessage::class)
        ];

        $resource = new Topic($test['topicArn'], $test['token']);

        $this->assertNull($resource->getId());
        $this->assertSame($test['topicArn'], $resource->getTopicArn());
        $this->assertSame($test['token'], $resource->getToken());
    }
}
