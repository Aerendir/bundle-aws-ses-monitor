<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\BounceRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\ComplaintRepositoryInterface;

/**
 * Base class to test bounced and complained address filtering.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class PluginFilterTestBase extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject $bouncedMock */
    protected $bouncedMock;

    /** @var array $bouncesConfig */
    protected $bouncesConfig;
    protected $bouncesRepo;

    /** @var \PHPUnit_Framework_MockObject_MockObject $complainedMock */
    protected $complainedMock;

    /** @var array $complaintsConfig */
    protected $complaintsConfig;
    protected $complaintsRepo;

    protected $orm;
    protected $event;

    /** @var \PHPUnit_Framework_MockObject_MockObject $message */
    protected $message;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bouncesConfig = [
            'filter' => [
                'enabled'             => true,
                'max_bounces'         => 5,
                'soft_blacklist_time' => 'forever',
                'hard_blacklist_time' => 'forever',
                'force_send'          => false
            ]
        ];
        $this->bouncedMock = $this->getMockBuilder(Bounce::class)->disableOriginalConstructor()->getMock();

        $this->complaintsConfig = [
            'filter' => [
                'enabled'    => true,
                'force_send' => false
            ]
        ];
        $this->complainedMock = $this->getMockBuilder(Complaint::class)->disableOriginalConstructor()->getMock();

        $map = [
            ['bounced@example.com', $this->bouncedMock],
            ['complained@example.com', $this->complainedMock],
            ['valid@example.com', null],
            ['valid2@example.com', null]
        ];

        $recipientsTo  = ['complained@example.com' => null, 'bounced@example.com' => null, 'valid2@example.com' => null, 'valid@example.com' => null];
        $recipientsCc  = ['valid@example.com' => null, 'complained@example.com' => null, 'bounced@example.com' => null, 'valid2@example.com' => null];
        $recipientsBcc = null;

        $this->bouncesRepo = $this->createMock(BounceRepositoryInterface::class);
        $this->bouncesRepo->expects($this->any())
            ->method('findOneByEmail')
            ->will($this->returnValueMap($map));

        $this->complaintsRepo = $this->createMock(ComplaintRepositoryInterface::class);
        $this->complaintsRepo->expects($this->any())
            ->method('findOneByEmail')
            ->will($this->returnValueMap($map));

        $this->orm = $this->createMock(EntityManager::class);
        $this->orm
            ->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($this->bouncesRepo, $this->complaintsRepo);

        $this->message = $this->createMock(\Swift_Mime_Message::class);

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
    }

    public function confirmThatNull()
    {
        $this->assertNull(func_get_arg(0));
    }

    public function confirmNoOneRemoved()
    {
        $recipients = func_get_arg(0);
        $this->assertArrayHasKey('valid@example.com', $recipients);
        $this->assertArrayHasKey('valid2@example.com', $recipients);
        $this->assertArrayHasKey('complained@example.com', $recipients);
        $this->assertArrayHasKey('bounced@example.com', $recipients);
    }

    public function confirmBouncedRemoved()
    {
        $recipients = func_get_arg(0);
        $this->assertArrayHasKey('valid@example.com', $recipients);
        $this->assertArrayHasKey('valid2@example.com', $recipients);
        $this->assertArrayHasKey('complained@example.com', $recipients);
        $this->assertArrayNotHasKey('bounced@example.com', $recipients);
    }

    public function confirmComplainedRemoved()
    {
        $recipients = func_get_arg(0);
        $this->assertArrayHasKey('valid@example.com', $recipients);
        $this->assertArrayHasKey('valid2@example.com', $recipients);
        $this->assertArrayHasKey('bounced@example.com', $recipients);
        $this->assertArrayNotHasKey('complained@example.com', $recipients);
    }
}
