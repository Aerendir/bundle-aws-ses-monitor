<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests;

use Doctrine\Common\Persistence\ObjectManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\BounceRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\ComplaintRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

/**
 * Test filtering of both bounced and complained addresses.
 */
class PluginFilterTest extends PluginFilterTestBase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->message
            ->expects($this->once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmBothRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmBothRemoved']);

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

    public function testRecipientsAreFilteredAll()
    {
        $filter = new MonitorFilterPlugin($this->om, $this->bouncesConfig, $this->complaintsConfig);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }
}
