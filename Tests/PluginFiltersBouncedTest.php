<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

/**
 * Tests the filtering of bounced addresses.
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
            ->expects($this->once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder(\Swift_Events_SendEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects($this->once())
            ->method('getMessage')
            ->willReturn($this->message);

        $filter = new MonitorFilterPlugin($this->om, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }

    public function testTemporaryAsHard()
    {
        $this->markTestIncomplete(
            'Move this to controller test'
        );
    }

    public function testFiltersMaxBounced()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testTemporaryBlacklistTime()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testHardBlacklistTime()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testForcedSending()
    {
        $this->bouncesConfig['filter']['force_send'] = true;

        $this->message
            ->expects($this->once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder(\Swift_Events_SendEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects($this->once())
            ->method('getMessage')
            ->willReturn($this->message);

        $filter = new MonitorFilterPlugin($this->om, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }
}
