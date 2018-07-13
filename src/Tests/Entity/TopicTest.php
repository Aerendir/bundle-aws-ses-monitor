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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;

/**
 * Tests the Topic entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class TopicTest extends TestCase
{
    public function testTopic()
    {
        $test = ['topicArn' => 'arn:aws:sns:us-west-2:111122223333:TestTopic'];

        $resource = new Topic($test['topicArn']);

        self::assertSame($test['topicArn'], $resource->getTopicArn());
    }
}
