<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\EmailStatusRepository;

/**
 * Base class to test bounced and complained address filtering.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class PluginFilterTestBase extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject $bouncedMock */
    protected $mockBouncedEmailStatus;
    protected $mockComplainedEmailStatus;
    protected $mockSuccessEmailStatus;
    protected $mockOotoEmailStatus;
    protected $mockSuppressedEmailStatus;

    /** @var array $bouncesConfig */
    protected $bouncesConfig;

    /** @var array $complaintsConfig */
    protected $complaintsConfig;
    protected $orm;
    protected $event;

    /** @var \PHPUnit_Framework_MockObject_MockObject $message */
    protected $message;

    private $emailStatusRepo;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bouncesConfig = [
            'filter' => [
                'enabled'             => true,
                'max_bounces'         => 5,
                'soft_as_hard'        => true,
                'soft_blacklist_time' => 'forever', // NOT IMPLEMENTED YET
                'hard_blacklist_time' => 'forever', // NOT IMPLEMENTED YET
                'force_send'          => false
            ]
        ];

        $this->complaintsConfig = [
            'filter' => [
                'enabled'    => true,
                'force_send' => false
            ]
        ];

        $this->mockBouncedEmailStatus = $this->createMock(EmailStatus::class);
        $this->mockBouncedEmailStatus->method('getEmailAddress')->willReturn('bounced@example.com');
        $this->mockBouncedEmailStatus->method('getHardBouncesCount')->willReturn(3);
        $this->mockBouncedEmailStatus->method('getSoftBouncesCount')->willReturn(3);

        $this->mockComplainedEmailStatus = $this->createMock(EmailStatus::class);
        $this->mockComplainedEmailStatus->method('getEmailAddress')->willReturn('complained@example.com');
        $this->mockComplainedEmailStatus->method('getComplaintsCount')->willReturn(1);

        $this->mockOotoEmailStatus = $this->createMock(EmailStatus::class);
        $this->mockOotoEmailStatus->method('getEmailAddress')->willReturn('ooto@example.com');

        $this->mockSuccessEmailStatus = $this->createMock(EmailStatus::class);
        $this->mockSuccessEmailStatus->method('getEmailAddress')->willReturn('success@example.com');

        $this->mockSuppressedEmailStatus = $this->createMock(EmailStatus::class);
        $this->mockSuppressedEmailStatus->method('getEmailAddress')->willReturn('suppressed@example.com');

        $map = [
            ['bounced@example.com', $this->mockBouncedEmailStatus],
            ['complained@example.com', $this->mockComplainedEmailStatus],
            ['ooto@example.com', $this->mockOotoEmailStatus],
            ['success@example.com', $this->mockSuccessEmailStatus],
            ['suppressed@example.com', $this->mockSuppressedEmailStatus],
        ];

        $recipientsTo  = ['bounced@example.com' => null, 'complained@example.com' => null, 'ooto@example.com' => null, 'success@example.com' => null, 'suppressed@example.com' => null];
        $recipientsCc  = ['bounced@example.com' => null, 'complained@example.com' => null, 'ooto@example.com' => null, 'success@example.com' => null, 'suppressed@example.com' => null];
        $recipientsBcc = null;

        $this->emailStatusRepo = $this->createMock(EmailStatusRepository::class);
        $this->emailStatusRepo->expects(self::any())
            ->method('findOneByEmailAddress')
            ->will(self::returnValueMap($map));

        $this->orm = $this->createMock(EntityManager::class);
        $this->orm
            ->expects(self::once())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($this->emailStatusRepo);

        $this->message = $this->createMock(\Swift_Message::class);

        $this->message
            ->expects(self::once())
            ->method('getTo')
            ->willReturn($recipientsTo);

        $this->message
            ->expects(self::once())
            ->method('getCc')
            ->willReturn($recipientsCc);

        $this->message
            ->expects(self::once())
            ->method('getBcc')
            ->willReturn($recipientsBcc);
    }

    public function confirmThatNull()
    {
        self::assertNull(func_get_arg(0));
    }

    public function confirmNoOneRemoved()
    {
        $recipients = func_get_arg(0);
        self::assertArrayHasKey('bounced@example.com', $recipients);
        self::assertArrayHasKey('complained@example.com', $recipients);
        self::assertArrayHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayHasKey('suppressed@example.com', $recipients);
    }

    public function confirmBouncedRemoved()
    {
        $recipients = func_get_arg(0);
        self::assertArrayNotHasKey('bounced@example.com', $recipients);
        self::assertArrayHasKey('complained@example.com', $recipients);
        self::assertArrayHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayHasKey('suppressed@example.com', $recipients);
    }

    public function confirmComplainedRemoved()
    {
        $recipients = func_get_arg(0);
        self::assertArrayHasKey('bounced@example.com', $recipients);
        self::assertArrayNotHasKey('complained@example.com', $recipients);
        self::assertArrayHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayHasKey('suppressed@example.com', $recipients);
    }
}
