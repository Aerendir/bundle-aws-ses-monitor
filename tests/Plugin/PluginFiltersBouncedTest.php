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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

/**
 * Tests the filtering of bounced addresses.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class PluginFiltersBouncedTest extends PluginFilterTestBase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Deactivate complaints filtering
        $this->complaintsConfig['filter']['enabled'] = false;
    }

    public function testBouncedFilterDisabled()
    {
        $this->bouncesConfig['filter']['enabled'] = false;

        $this->message
            ->expects(self::once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder(\Swift_Events_SendEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects(self::once())
            ->method('getMessage')
            ->willReturn($this->message);

        $filter = new MonitorFilterPlugin($this->orm, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }

    public function testTemporaryAsHard()
    {
        self::markTestIncomplete('Move this to controller test');
    }

    /**
     * Tests the max num of bounces allowed.
     *
     * Uses soft_as_hard = true.
     */
    public function testFiltersMaxBounced()
    {
        $this->message
            ->expects(self::once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmBouncedRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmBouncedRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder(\Swift_Events_SendEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects(self::once())
            ->method('getMessage')
            ->willReturn($this->message);

        $filter = new MonitorFilterPlugin($this->orm, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }

    public function testSoftBlacklistTimeForever()
    {
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSoftBlacklistTimeCustom()
    {
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    public function testHardBlacklistTimeForever()
    {
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    public function testHardBlacklistTimeCustom()
    {
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    public function testForcedSending()
    {
        $this->bouncesConfig['filter']['force_send'] = true;

        $this->message
            ->expects(self::once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder(\Swift_Events_SendEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects(self::once())
            ->method('getMessage')
            ->willReturn($this->message);

        $filter = new MonitorFilterPlugin($this->orm, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }
}
