<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\EmailStatus;

/**
 * Tests the EmailStatus entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class EmailStatusTest extends \PHPUnit_Framework_TestCase
{
    public function testBounces()
    {
        $testEmail = 'test@example.com';

        $resource = new EmailStatus($testEmail);
        $this->assertSame($testEmail, $resource->getEmailAddress());
        $this->assertInstanceOf(ArrayCollection::class, $resource->getBounces());
        $this->assertSame(0, $resource->getBounces()->count());

        // Test hard bounce
        $now = new \DateTime();

        $mockHardBounce = $this->createMock(Bounce::class);
        $mockHardBounce->method('getType')->willReturn(Bounce::TYPE_PERMANENT);
        $mockHardBounce->method('getBouncedOn')->willReturn($now);

        $resource->addBounce($mockHardBounce);

        $returnedHardBounce = $resource->getBounces()->first();
        $this->assertSame($mockHardBounce, $returnedHardBounce);
        //$this->assertSame($testEmail, $returnedHardBounce->getEmailAddress());
        $this->assertSame(Bounce::TYPE_PERMANENT, $resource->getLastBounceType());
        $this->assertSame($now, $resource->getLastTimeBounced());
        $this->assertSame(1, $resource->getBounces()->count());
        $this->assertSame(1, $resource->getHardBouncesCount());

        // Test soft bounce
        $aLittleBitAfter = new \DateTime();

        $mockSoftBounce = $this->createMock(Bounce::class);
        $mockSoftBounce->method('getType')->willReturn(Bounce::TYPE_TRANSIENT);
        $mockSoftBounce->method('getBouncedOn')->willReturn($aLittleBitAfter);

        $resource->addBounce($mockSoftBounce);

        $returnedSoftBounce = $resource->getBounces()->last();
        $this->assertSame($mockSoftBounce, $returnedSoftBounce);
        //$this->assertSame($testEmail, $returnedSoftBounce->getEmailAddress());
        $this->assertSame(Bounce::TYPE_TRANSIENT, $resource->getLastBounceType());
        $this->assertSame($aLittleBitAfter, $resource->getLastTimeBounced());
        $this->assertSame(2, $resource->getBounces()->count());
        $this->assertSame(1, $resource->getHardBouncesCount());
        $this->assertSame(1, $resource->getSoftBouncesCount());
    }

    public function testComplaints()
    {
        $testEmail = 'test@example.com';

        $resource = new EmailStatus($testEmail);
        $this->assertSame($testEmail, $resource->getEmailAddress());
        $this->assertInstanceOf(ArrayCollection::class, $resource->getComplaints());
        $this->assertSame(0, $resource->getComplaints()->count());

        // Test Complaint
        $now = new \DateTime();

        $mockComplaint = $this->createMock(Complaint::class);
        $mockComplaint->method('getComplainedOn')->willReturn($now);

        $resource->addComplaint($mockComplaint);

        $returnedComplaint = $resource->getComplaints()->first();
        $this->assertSame($mockComplaint, $returnedComplaint);
        //$this->assertSame($testEmail, $returnedHardBounce->getEmailAddress());
        $this->assertSame($now, $resource->getLastTimeComplained());
        $this->assertSame(1, $resource->getComplaints()->count());
        $this->assertSame(1, $resource->getComplaintsCount());
    }

    public function testDeliveries()
    {
        $testEmail = 'test@example.com';

        $resource = new EmailStatus($testEmail);
        $this->assertSame($testEmail, $resource->getEmailAddress());
        $this->assertInstanceOf(ArrayCollection::class, $resource->getDeliveries());
        $this->assertSame(0, $resource->getDeliveries()->count());

        // Test Complaint
        $now = new \DateTime();

        $mockDelivery = $this->createMock(Delivery::class);
        $mockDelivery->method('getDeliveredOn')->willReturn($now);

        $resource->addDelivery($mockDelivery);

        $returnedDelivery = $resource->getDeliveries()->first();
        $this->assertSame($mockDelivery, $returnedDelivery);
        //$this->assertSame($testEmail, $returnedHardBounce->getEmailAddress());
        $this->assertSame($now, $resource->getLastTimeDelivered());
        $this->assertSame(1, $resource->getDeliveries()->count());
        $this->assertSame(1, $resource->getDeliveriesCount());
    }
}
