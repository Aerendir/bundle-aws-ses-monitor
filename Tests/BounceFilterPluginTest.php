<?php
use Doctrine\Common\Persistence\ObjectManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\BounceRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\ComplaintRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

class BounceFilterPluginTest extends \PHPUnit_Framework_TestCase
{
    private $bounced;
    private $complained;
    private $om;
    private $bounceRepo;
    private $complainedRepo;
    private $event;
    private $message;

    protected function setUp()
    {
        $this->bounced = $this->getMockBuilder(Bounce::class)->disableOriginalConstructor()->getMock();

        $this->complained = $this->getMockBuilder(Complaint::class)->disableOriginalConstructor()->getMock();

        $map = [
            ['bounced@example.com', $this->bounced],
            ['complained@example.com', $this->complained],
            ['valid@example.com', null],
            ['valid2@example.com', null]
        ];

        $recipientsTo = ['complained@example.com' => null, 'bounced@example.com' => null, 'valid2@example.com' => null, 'valid@example.com' => null];
        $recipientsCc = ['valid@example.com' => null, 'complained@example.com' => null, 'bounced@example.com' => null, 'valid2@example.com' => null];
        $recipientsBcc = null;

        $this->bounceRepo = $this->createMock(BounceRepositoryInterface::class);
        $this->bounceRepo->expects($this->any())
            ->method('findBounceByEmail')
            ->will($this->returnValueMap($map));

        $this->complainedRepo = $this->createMock(ComplaintRepositoryInterface::class);
        $this->complainedRepo->expects($this->any())
            ->method('findComplaintByEmail')
            ->will($this->returnValueMap($map));

        $this->om = $this->createMock(ObjectManager::class);
        $this->om
            ->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($this->bounceRepo, $this->complainedRepo);

        $this->message = $this->createMock('Swift_Mime_Message');

        $this->message
            ->expects($this->once())
            ->method('getTo')
            ->willReturn($recipientsTo);

        $this->message
            ->expects($this->once())
            ->method('getCc')
            ->willReturn($recipientsCc);

        $this->message
            ->expects($this->once())
            ->method('getBcc')
            ->willReturn($recipientsBcc);

        $this->message
            ->expects($this->once())
            ->method('setTo')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmBouncerRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmBouncerRemoved']);

        $this->message
            ->expects($this->once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback([$this, 'confirmThatNull']);

        $this->event = $this->getMockBuilder('Swift_Events_SendEvent')->disableOriginalConstructor()->getMock();
        $this->event->expects($this->once())
                ->method('getMessage')
                ->willReturn($this->message);
    }

    public function confirmThatNull()
    {
        $this->assertNull(func_get_arg(0));
    }

    public function confirmBouncerRemoved()
    {
        $recipients = func_get_arg(0);
        $this->assertArrayHasKey('valid@example.com', $recipients);
        $this->assertArrayHasKey('valid2@example.com', $recipients);
        $this->assertArrayNotHasKey('bounced@example.com', $recipients);
    }

    public function testRecipientsAreFilteredAll()
    {
        $filter = new MonitorFilterPlugin($this->om, true, '5');
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }
}
