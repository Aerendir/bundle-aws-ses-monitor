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
class TopicTest extends TestCase
{
    public function testTopic()
    {
        $test = [
            'name' => 'TestTopic',
            'arn'  => 'arn:aws:sns:us-west-2:111122223333:TestTopic',
        ];

        $resource = new Topic($test['name'], $test['arn']);

        self::assertSame($test['name'], $resource->getName());
        self::assertSame($test['arn'], $resource->getArn());
    }
}
