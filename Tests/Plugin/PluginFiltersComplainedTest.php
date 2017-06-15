<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

/**
 * Tests the filtering of complained addresses.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class PluginFiltersComplainedTest extends PluginFilterTestBase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Deactivate bounces filtering
        $this->bouncesConfig['filter']['enabled'] = false;
    }

    public function testComplainedFilterDisabled()
    {
        $this->complaintsConfig['filter']['enabled'] = false;

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

    public function testForcedSending()
    {
        $this->complaintsConfig['filter']['force_send'] = true;

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

    public function testComplainedAreFiltered()
    {
        $this->bouncesConfig['filter']['enabled'] = false;

        $this->message
            ->expects(self::once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmComplainedRemoved']);

        $this->message
            ->expects(self::once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmComplainedRemoved']);

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
