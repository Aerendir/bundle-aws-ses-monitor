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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email;

/**
 * Tests the Email entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class EmailStatusTest extends TestCase
{
    public function testBounces()
    {
        $testEmail = 'test@example.com';

        $resource = new Email($testEmail);
        self::assertSame($testEmail, $resource->getAddress());
        self::assertInstanceOf(ArrayCollection::class, $resource->getBounces());
        self::assertSame(0, $resource->getBounces()->count());

        // Test hard bounce
        $now = new \DateTime();

        $mockHardBounce = $this->createMock(Bounce::class);
        $mockHardBounce->method('getType')->willReturn(Bounce::TYPE_PERMANENT);
        $mockHardBounce->method('getBouncedOn')->willReturn($now);

        $resource->addBounce($mockHardBounce);

        self::assertSame(Bounce::TYPE_PERMANENT, $resource->getLastBounceType());
        self::assertSame($now, $resource->getLastTimeBounced());
        self::assertSame(1, $resource->getHardBouncesCount());

        // Test soft bounce
        $aLittleBitAfter = new \DateTime();

        $mockSoftBounce = $this->createMock(Bounce::class);
        $mockSoftBounce->method('getType')->willReturn(Bounce::TYPE_TRANSIENT);
        $mockSoftBounce->method('getBouncedOn')->willReturn($aLittleBitAfter);

        $resource->addBounce($mockSoftBounce);

        self::assertSame(Bounce::TYPE_TRANSIENT, $resource->getLastBounceType());
        self::assertSame($aLittleBitAfter, $resource->getLastTimeBounced());
        self::assertSame(1, $resource->getHardBouncesCount(), 'The total hard bounces count is wrong.');
        self::assertSame(1, $resource->getSoftBouncesCount(), 'The total soft bounces count is wrong.');
    }

    public function testComplaints()
    {
        $testEmail = 'test@example.com';

        $resource = new Email($testEmail);
        self::assertSame($testEmail, $resource->getAddress());
        self::assertInstanceOf(ArrayCollection::class, $resource->getComplaints());
        self::assertSame(0, $resource->getComplaints()->count());

        // Test Complaint
        $now = new \DateTime();

        $mockComplaint = $this->createMock(Complaint::class);
        $mockComplaint->method('getComplainedOn')->willReturn($now);

        $resource->addComplaint($mockComplaint);

        self::assertSame($now, $resource->getLastTimeComplained());
        self::assertSame(1, $resource->getComplaintsCount());
    }

    public function testDeliveries()
    {
        $testEmail = 'test@example.com';

        $resource = new Email($testEmail);
        self::assertSame($testEmail, $resource->getAddress());
        self::assertInstanceOf(ArrayCollection::class, $resource->getDeliveries());
        self::assertSame(0, $resource->getDeliveries()->count());

        // Test Complaint
        $now = new \DateTime();

        $mockDelivery = $this->createMock(Delivery::class);
        $mockDelivery->method('getDeliveredOn')->willReturn($now);

        $resource->addDelivery($mockDelivery);

        self::assertSame($now, $resource->getLastTimeDelivered());
        self::assertSame(1, $resource->getDeliveriesCount());
    }
}
