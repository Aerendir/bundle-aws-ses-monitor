<?php
use Shivas\BouncerBundle\Plugin\BouncerFilterPlugin;

class BounceFilterPluginPermanentTest extends \PHPUnit_Framework_TestCase
{
    private $bounced;
    private $om;
    private $bounceRepo;
    private $event;
    private $message;

    protected function setUp()
    {
        $this->bounced = $this->getMockBuilder('Shivas\BouncerBundle\Model\Bounce')->disableOriginalConstructor()->getMock();
        $this->bounced->method('isPermanent')->willReturn(true);

        $map = array(
            array('bounced@example.com', $this->bounced),
            array('valid@example.com', null),
            array('valid2@example.com', null)
        );

        $recipientsTo = array('bounced@example.com' => null, 'valid2@example.com' => null, 'valid@example.com' => null);
        $recipientsCc = array('valid@example.com' => null, 'bounced@example.com' => null, 'valid2@example.com' => null);
        $recipientsBcc = null;

        $this->bounceRepo = $this->createMock('Shivas\BouncerBundle\Model\BounceRepositoryInterface');
        $this->bounceRepo->expects($this->any())
            ->method('findBounceByEmail')
            ->will($this->returnValueMap($map));

        $this->om = $this->createMock('Doctrine\Common\Persistence\ObjectManager');
        $this->om
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->bounceRepo);

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
            ->willReturnCallback(array($this, 'confirmBouncerRemoved'));

        $this->message
            ->expects($this->once())
            ->method('setCc')
            ->withAnyParameters()
            ->willReturnCallback(array($this, 'confirmBouncerRemoved'));

        $this->message
            ->expects($this->once())
            ->method('setBcc')
            ->withAnyParameters()
            ->willReturnCallback(array($this, 'confirmThatNull'));

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

    public function testRecipientsAreFiltered()
    {
        $filter = new BouncerFilterPlugin($this->om, false);
        $filter->beforeSendPerformed($this->event);
        $filter->sendPerformed($this->event);
    }
}
