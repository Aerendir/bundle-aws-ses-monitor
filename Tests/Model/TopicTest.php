<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;

/**
 * Tests the Topic entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class TopicTest extends TestCase
{
    public function testTopic()
    {
        $test = [
            'topicArn' => 'test@example.com',
            'token'    => $this->createMock(MailMessage::class)
        ];

        $resource = new Topic($test['topicArn'], $test['token']);

        self::assertNull($resource->getId());
        self::assertSame($test['topicArn'], $resource->getTopicArn());
        self::assertSame($test['token'], $resource->getToken());
    }
}
