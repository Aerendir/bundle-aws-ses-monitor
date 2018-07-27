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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Util;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\Monitor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\EmailStatusAnalyzer;

/**
 * {@inheritdoc}
 */
class EmailStatusAnalyzerTest extends TestCase
{
    public function testIsComplained()
    {
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);
        $mockCollection  = $this->createMock(Collection::class);

        $mockCollection->expects(self::once())->method('count')->willReturn(1);
        $mockEmailStatus->expects(self::once())->method('getComplaints')->willReturn($mockCollection);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->isComplained($mockEmailStatus));
    }

    public function testIsBouncedHard()
    {
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(1);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->isBounced($mockEmailStatus, 1, false));
    }

    public function testIsBouncedWith()
    {
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(1);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(1);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->isBounced($mockEmailStatus, 2, true));
    }

    public function testIsBouncedNot()
    {
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(0);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertFalse($resource->isBounced($mockEmailStatus, 1, false));
    }

    public function testBouncedCannnotReceiveMessages()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);

        $mockMonitor->expects(self::once())->method('bouncesTrackingIsEnabled')->with($testIdentity)->willReturn(true);
        $mockMonitor->expects(self::once())->method('bouncesSendingIsForced')->with($testIdentity)->willReturn(false);
        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(1);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(1);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertFalse($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }

    public function testBouncedCanReceiveMessagesWithTrackingDisabled()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);

        $mockMonitor->expects(self::once())->method('bouncesTrackingIsEnabled')->with($testIdentity)->willReturn(false);
        $mockMonitor->expects(self::never())->method('bouncesSendingIsForced')->with($testIdentity);
        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(1);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(1);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }

    public function testBouncedCanReceiveMessagesWithSendingForced()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);

        $mockMonitor->expects(self::once())->method('bouncesTrackingIsEnabled')->with($testIdentity)->willReturn(true);
        $mockMonitor->expects(self::once())->method('bouncesSendingIsForced')->with($testIdentity)->willReturn(true);
        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(1);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(1);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }

    public function testComplainedCannnotReceiveMessages()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);
        $mockCollection  = $this->createMock(Collection::class);

        $mockCollection->expects(self::once())->method('count')->willReturn(1);

        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);
        $mockMonitor->expects(self::once())->method('complaintsTrackingIsEnabled')->with($testIdentity)->willReturn(true);
        $mockMonitor->expects(self::once())->method('complaintsSendingIsForced')->with($testIdentity)->willReturn(false);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getComplaints')->willReturn($mockCollection);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertFalse($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }

    public function testComplainedCanReceiveMessagesWithTrackingDisabled()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);
        $mockCollection  = $this->createMock(Collection::class);

        $mockCollection->expects(self::once())->method('count')->willReturn(1);

        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);
        $mockMonitor->expects(self::once())->method('complaintsTrackingIsEnabled')->with($testIdentity)->willReturn(false);
        $mockMonitor->expects(self::never())->method('complaintsSendingIsForced')->with($testIdentity);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getComplaints')->willReturn($mockCollection);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }

    public function testComplainedCanReceiveMessagesWithSendingForced()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);
        $mockCollection  = $this->createMock(Collection::class);

        $mockCollection->expects(self::once())->method('count')->willReturn(1);

        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);
        $mockMonitor->expects(self::once())->method('complaintsTrackingIsEnabled')->with($testIdentity)->willReturn(true);
        $mockMonitor->expects(self::once())->method('complaintsSendingIsForced')->with($testIdentity)->willReturn(true);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getComplaints')->willReturn($mockCollection);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }

    public function testHealthyCanReceiveMessages()
    {
        $testIdentity    = 'hello@serendipityhq.com';
        $mockMonitor     = $this->createMock(Monitor::class);
        $mockEmailStatus = $this->createMock(EmailStatus::class);
        $mockCollection  = $this->createMock(Collection::class);

        $mockCollection->expects(self::once())->method('count')->willReturn(0);

        $mockMonitor->expects(self::exactly(1))->method('findConfiguredIdentity')->with($testIdentity, 'bounces')->willReturn([
            'filter' => [
                'max_bounces'  => 2,
                'soft_as_hard' => true,
            ],
        ]);

        $mockEmailStatus->expects(self::once())->method('getHardBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getSoftBouncesCount')->willReturn(0);
        $mockEmailStatus->expects(self::once())->method('getComplaints')->willReturn($mockCollection);

        $resource = new EmailStatusAnalyzer($mockMonitor);

        self::assertTrue($resource->canReceiveMessages($mockEmailStatus, $testIdentity));
    }
}
