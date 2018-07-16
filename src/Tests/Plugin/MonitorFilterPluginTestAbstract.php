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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;

/**
 * Abstract base class to test the monitor filter plugin.
 */
class MonitorFilterPluginTestAbstract extends TestCase
{
    /** @var EmailStatusManager&MockObject $mockEmailStatusManager */
    protected $mockEmailStatusManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockEmailStatusManager = $this->createMock(EmailStatusManager::class);
        $this->mockEmailStatusManager->method('loadEmailStatus')->will(self::returnValueMap($this->getEmailStatusMap()));
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

    public function confirmHardBouncedRemoved()
    {
        $recipients = func_get_arg(0);
        self::assertArrayNotHasKey('bounced@example.com', $recipients);
        self::assertArrayHasKey('complained@example.com', $recipients);
        self::assertArrayHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayHasKey('suppressed@example.com', $recipients);
    }

    public function confirmHardAndSoftBouncedRemoved()
    {
        $recipients = func_get_arg(0);
        self::assertArrayNotHasKey('bounced@example.com', $recipients);
        self::assertArrayHasKey('complained@example.com', $recipients);
        self::assertArrayNotHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayHasKey('suppressed@example.com', $recipients);
    }

    public function confirmOnlyComplainedAreRemoved()
    {
        $recipients = func_get_arg(0);
        self::assertArrayHasKey('bounced@example.com', $recipients);
        self::assertArrayNotHasKey('complained@example.com', $recipients);
        self::assertArrayHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayHasKey('suppressed@example.com', $recipients);
    }

    /**
     * @return array
     */
    protected function getDefaultBouncesConfig(): array
    {
        return [
            'filter' => [
                'enabled'             => true,
                'max_bounces'         => 5,
                'soft_as_hard'        => false,
                'soft_blacklist_time' => 'forever', // NOT IMPLEMENTED YET
                'hard_blacklist_time' => 'forever', // NOT IMPLEMENTED YET
                'force_send'          => false,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getDefaultComplaintsConfig(): array
    {
        return [
            'filter' => [
                'enabled'    => true,
                'force_send' => false,
            ],
        ];
    }

    /**
     * @return MockObject|\Swift_Message
     */
    protected function getDefaultMessage()
    {
        $recipients = ['bounced@example.com' => null, 'complained@example.com' => null, 'ooto@example.com' => null, 'success@example.com' => null, 'suppressed@example.com' => null];
        $message    = $this->createMock(\Swift_Message::class);
        $message->expects(self::exactly(2))->method('getTo')->willReturn($recipients);
        $message->expects(self::exactly(2))->method('getCc')->willReturn($recipients);
        $message->expects(self::exactly(2))->method('getBcc')->willReturn($recipients);

        return $message;
    }

    /**
     * Configures the EmailStatusManager to return the mocked EmailStatus objects each
     * time the EmailStatusManager::loadOrCreateEmailStatus() method is called.
     *
     * @return array
     */
    private function getEmailStatusMap(): array
    {
        $mockBouncedEmailStatus = $this->createMock(EmailStatus::class);
        $mockBouncedEmailStatus->method('getAddress')->willReturn('bounced@example.com');
        $mockBouncedEmailStatus->method('getHardBouncesCount')->willReturn(3);
        $mockBouncedEmailStatus->method('getSoftBouncesCount')->willReturn(3);

        $mockComplainedCollection        = $this->getMockBuilder(ArrayCollection::class)->getMock();
        $mockComplainedCollection->method('count')->willReturn(1);
        $mockComplainedEmailStatus       = $this->createMock(EmailStatus::class);
        $mockComplainedEmailStatus->method('getAddress')->willReturn('complained@example.com');
        $mockComplainedEmailStatus->method('getComplaints')->willReturn($mockComplainedCollection);

        $mockOotoEmailStatus = $this->createMock(EmailStatus::class);
        $mockOotoEmailStatus->method('getAddress')->willReturn('ooto@example.com');

        $mockSuccessEmailStatus = $this->createMock(EmailStatus::class);
        $mockSuccessEmailStatus->method('getAddress')->willReturn('success@example.com');

        $mockSuppressedEmailStatus = $this->createMock(EmailStatus::class);
        $mockSuppressedEmailStatus->method('getAddress')->willReturn('suppressed@example.com');

        return [
            ['bounced@example.com', $mockBouncedEmailStatus],
            ['complained@example.com', $mockComplainedEmailStatus],
            ['ooto@example.com', $mockOotoEmailStatus],
            ['success@example.com', $mockSuccessEmailStatus],
            ['suppressed@example.com', $mockSuppressedEmailStatus],
        ];
    }
}
