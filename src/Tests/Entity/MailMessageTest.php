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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;

/**
 * Tests the MailMessage entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class MailMessageTest extends TestCase
{
    public function testTopic()
    {
        $test = [
            'messageId'        => 'test-message-id',
            'sentOn'           => $this->createMock(\DateTime::class),
            'sentFrom'         => 'test@example.com',
            'sourceArn'        => 'test-source-arn',
            'sendingAccountId' => 'test-sending-account-id',
            'headers'          => 'test-headers',
            'commonHeaders'    => 'test-common-headers',
        ];

        $mockBounce    = $this->createMock(Bounce::class);
        $mockComplaint = $this->createMock(Complaint::class);
        $mockDelivery  = $this->createMock(Delivery::class);

        $resource = new MailMessage();
        $resource->addBounce($mockBounce)
            ->addComplaint($mockComplaint)
            ->addDelivery($mockDelivery)
            ->setMessageId($test['messageId'])
            ->setSentOn($test['sentOn'])
            ->setSentFrom($test['sentFrom'])
            ->setSourceArn($test['sourceArn'])
            ->setSendingAccountId($test['sendingAccountId'])
            ->setHeaders($test['headers'])
            ->setCommonHeaders($test['commonHeaders']);

        self::assertNull($resource->getId());
        self::assertSame($test['messageId'], $resource->getMessageId());
        self::assertSame($test['sentOn'], $resource->getSentOn());
        self::assertSame($test['sentFrom'], $resource->getSentFrom());
        self::assertSame($test['sourceArn'], $resource->getSourceArn());
        self::assertSame($test['sendingAccountId'], $resource->getSendingAccountId());
        self::assertSame($test['headers'], $resource->getHeaders());
        self::assertSame($test['commonHeaders'], $resource->getCommonHeaders());
        self::assertSame(1, $resource->getBounces()->count());
        self::assertSame(1, $resource->getComplaints()->count());
        self::assertSame(1, $resource->getDeliveries()->count());
        self::assertSame($mockBounce, $resource->getBounces()->first());
        self::assertSame($mockComplaint, $resource->getComplaints()->first());
        self::assertSame($mockDelivery, $resource->getDeliveries()->first());
    }
}
