<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

/**
 * Tests the filtering of complained addresses.
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

        $this->message
            ->expects($this->once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmComplainedRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmComplainedRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder(\Swift_Events_SendEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects($this->once())
            ->method('getMessage')
            ->willReturn($this->message);
    }

    public function testComplainedRecipientsAreFiltered()
    {
        $filter = new MonitorFilterPlugin($this->om, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }
}
