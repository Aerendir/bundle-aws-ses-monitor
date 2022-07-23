<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;

/**
 * Tests the EmailStatus entity.
 */
final class EmailStatusTest extends TestCase
{
    public function testBounces(): void
    {
        $testEmail = 'test@example.com';

        $resource = new EmailStatus($testEmail);
        self::assertSame($testEmail, $resource->getAddress());
        self::assertInstanceOf(ArrayCollection::class, $resource->getBounces());
        self::assertSame(0, $resource->getBounces()->count());

        // Test hard bounce
        $now = new \DateTime();

        $mockHardBounce = $this->createMock(Bounce::class);
        $mockHardBounce->method('getType')->willReturn(Bounce::TYPE_PERMANENT);
        $mockHardBounce->method('getBouncedOn')->willReturn($now);
        $mockHardBounce->method('getFeedbackId')->willReturn('bounce-1');

        $resource->addBounce($mockHardBounce);

        self::assertSame(Bounce::TYPE_PERMANENT, $resource->getLastBounceType());
        self::assertSame($now, $resource->getLastTimeBounced());
        self::assertSame(1, $resource->getHardBouncesCount());

        // Test soft bounce
        $aLittleBitAfter = new \DateTime();

        $mockSoftBounce = $this->createMock(Bounce::class);
        $mockSoftBounce->method('getType')->willReturn(Bounce::TYPE_TRANSIENT);
        $mockSoftBounce->method('getBouncedOn')->willReturn($aLittleBitAfter);
        $mockSoftBounce->method('getFeedbackId')->willReturn('bounce-2');

        $resource->addBounce($mockSoftBounce);

        self::assertSame(Bounce::TYPE_TRANSIENT, $resource->getLastBounceType());
        self::assertSame($aLittleBitAfter, $resource->getLastTimeBounced());
        self::assertSame(1, $resource->getHardBouncesCount(), 'The total hard bounces count is wrong.');
        self::assertSame(1, $resource->getSoftBouncesCount(), 'The total soft bounces count is wrong.');
    }

    public function testComplaints(): void
    {
        $testEmail = 'test@example.com';

        $resource = new EmailStatus($testEmail);
        self::assertSame($testEmail, $resource->getAddress());
        self::assertInstanceOf(ArrayCollection::class, $resource->getComplaints());
        self::assertSame(0, $resource->getComplaints()->count());

        // Test Complaint
        $now = new \DateTime();

        $mockComplaint = $this->createMock(Complaint::class);
        $mockComplaint->method('getComplainedOn')->willReturn($now);
        $mockComplaint->method('getFeedbackId')->willReturn('complaint-1');

        $resource->addComplaint($mockComplaint);

        self::assertSame($now, $resource->getLastTimeComplained());
        self::assertSame(1, $resource->getComplaints()->count());

        // Test another Complaint
        $aLittleBitLater = new \DateTime();

        $mockComplaint2 = $this->createMock(Complaint::class);
        $mockComplaint2->method('getComplainedOn')->willReturn($aLittleBitLater);
        $mockComplaint2->method('getFeedbackId')->willReturn('complaint-2');

        $resource->addComplaint($mockComplaint2);

        self::assertSame($aLittleBitLater, $resource->getLastTimeComplained());
        self::assertSame(2, $resource->getComplaints()->count());
    }

    public function testDeliveries(): void
    {
        $testEmail = 'test@example.com';

        $resource = new EmailStatus($testEmail);
        self::assertSame($testEmail, $resource->getAddress());
        self::assertInstanceOf(ArrayCollection::class, $resource->getDeliveries());
        self::assertSame(0, $resource->getDeliveries()->count());

        // Test Delivery
        $now = new \DateTime();

        $mockDelivery = $this->createMock(Delivery::class);
        $mockDelivery->method('getDeliveredOn')->willReturn($now);

        $resource->addDelivery($mockDelivery);

        self::assertSame($now, $resource->getLastTimeDelivered());
        self::assertSame(1, $resource->getDeliveries()->count());

        // Test another Delivery (add one second because the script is too fast and the two timestamps are the same)
        $aLittleBitLater = (new \DateTime())->modify('+1 second');

        $mockDelivery2 = $this->createMock(Delivery::class);
        $mockDelivery2->method('getDeliveredOn')->willReturn($aLittleBitLater);

        $resource->addDelivery($mockDelivery2);

        self::assertSame($aLittleBitLater, $resource->getLastTimeDelivered());
        self::assertSame(2, $resource->getDeliveries()->count());
    }
}
