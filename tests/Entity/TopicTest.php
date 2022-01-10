<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;

/**
 * Tests the Topic entity.
 */
final class TopicTest extends TestCase
{
    /**
     * @var array<string, string>
     */
    private const TEST = [
        'name' => 'TestTopic',
        'arn'  => 'arn:aws:sns:us-west-2:111122223333:TestTopic',
    ];

    public function testTopic(): void
    {
        $resource = new Topic(self::TEST['name'], self::TEST['arn']);
        self::assertSame(self::TEST['name'], $resource->getName());
        self::assertSame(self::TEST['arn'], $resource->getArn());
    }
}
