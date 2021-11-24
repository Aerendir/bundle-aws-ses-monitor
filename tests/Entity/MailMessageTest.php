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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;

/**
 * Tests the MailMessage entity.
 */
final class MailMessageTest extends TestCase
{
    public function testTopic(): void
    {
        $test = [
            'messageId'        => 'test-message-id',
            'timestamp'        => (new \DateTime())->format('Y-m-d H:i:s'),
            'source'           => 'test@example.com',
            'sourceArn'        => 'test-source-arn',
            'sendingAccountId' => 'test-sending-account-id',
            'headers'          => 'test-headers',
            'commonHeaders'    => 'test-common-headers',
        ];

        $mockBounce    = $this->createMock(Bounce::class);
        $mockComplaint = $this->createMock(Complaint::class);
        $mockDelivery  = $this->createMock(Delivery::class);

        $resource = MailMessage::create($test)
                               ->addBounce($mockBounce)
                               ->addComplaint($mockComplaint)
                               ->addDelivery($mockDelivery);

        self::assertSame($test['messageId'], $resource->getMessageId());
        self::assertSame($test['timestamp'], $resource->getSentOn()->format('Y-m-d H:i:s'));
        self::assertSame($test['source'], $resource->getSentFrom());
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
