<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\EmailStatusAnalyzer;

final class MonitorFilterPluginTest extends TestCase
{
    /**
     * @return never
     */
    public function testBeforeSendPerformedWithoutRecipientsDoesNothing(): void
    {
        $mockEmailStatusAnalyzer = $this->createMock(EmailStatusAnalyzer::class);
        $mockEmailStatusManager  = $this->createMock(EmailStatusManager::class);
        $mockMessage             = $this->createMock(\Swift_Message::class);
        $mockEvent               = $this->createMock(\Swift_Events_SendEvent::class);

        $mockEvent->expects(self::once())->method('getMessage')->willReturn($mockMessage);
        $mockMessage->expects(self::once())->method('getFrom')->willReturn(null);
        $mockMessage->expects(self::never())->method('setTo')->willReturn(null);
        $mockMessage->expects(self::never())->method('setCc')->willReturn(null);
        $mockMessage->expects(self::never())->method('setBcc')->willReturn(null);

        $resource = new MonitorFilterPlugin($mockEmailStatusAnalyzer, $mockEmailStatusManager);

        $resource->beforeSendPerformed($mockEvent);
    }

    /**
     * @noRector \Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector
     * @return never
     */
    public function testBeforeSendPerformedWithGetToRecipients(): void
    {
        $mockEmailStatusAnalyzer = $this->createMock(EmailStatusAnalyzer::class);
        $mockEmailStatusAnalyzer->expects(self::exactly(5))->method('canReceiveMessages')->willReturnCallback([$this, 'canReceiveMessages']);
        $mockEmailStatusManager = $this->createMock(EmailStatusManager::class);
        $mockEmailStatusManager->expects(self::exactly(5))->method('loadEmailStatus')->willReturnMap($this->getEmailStatusMap());
        $mockEvent   = $this->createMock(\Swift_Events_SendEvent::class);
        $mockMessage = $this->getMessageWithRecipients();

        $mockEvent->expects(self::once())->method('getMessage')->willReturn($mockMessage);
        $mockEvent->expects(self::once())->method('setFailedRecipients')->willReturnCallback([$this, 'confirmAllButSuccessAreBlacklisted']);
        $mockMessage->expects(self::exactly(2))->method('getTo')->willReturn($this->getRecipients());
        $mockMessage->expects(self::exactly(1))->method('getCc')->willReturn(null);
        $mockMessage->expects(self::exactly(1))->method('getBcc')->willReturn(null);
        $mockMessage->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmAllButSuccessAreRemoved']);
        $mockMessage->expects(self::never())->method('setCc');
        $mockMessage->expects(self::never())->method('setBcc');

        $resource = new MonitorFilterPlugin($mockEmailStatusAnalyzer, $mockEmailStatusManager);

        $resource->beforeSendPerformed($mockEvent);
        $resource->sendPerformed($mockEvent);
    }

    /**
     * @noRector \Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector
     * @return never
     */
    public function testBeforeSendPerformedWithGetCcRecipients(): void
    {
        $mockEmailStatusAnalyzer = $this->createMock(EmailStatusAnalyzer::class);
        $mockEmailStatusManager  = $this->createMock(EmailStatusManager::class);
        $mockEvent               = $this->createMock(\Swift_Events_SendEvent::class);
        $mockMessage             = $this->getMessageWithRecipients();

        $mockEmailStatusAnalyzer->expects(self::exactly(5))->method('canReceiveMessages')->willReturnCallback([$this, 'canReceiveMessages']);
        $mockEmailStatusManager->expects(self::exactly(5))->method('loadEmailStatus')->willReturnMap($this->getEmailStatusMap());
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($mockMessage);
        $mockEvent->expects(self::once())->method('setFailedRecipients')->willReturnCallback([$this, 'confirmAllButSuccessAreBlacklisted']);
        $mockMessage->expects(self::exactly(1))->method('getTo')->willReturn(null);
        $mockMessage->expects(self::exactly(2))->method('getCc')->willReturn($this->getRecipients());
        $mockMessage->expects(self::exactly(1))->method('getBcc')->willReturn(null);
        $mockMessage->expects(self::never())->method('setTo')->willReturnCallback([$this, 'confirmAllButSuccessAreRemoved']);
        $mockMessage->expects(self::once())->method('setCc');
        $mockMessage->expects(self::never())->method('setBcc');

        $resource = new MonitorFilterPlugin($mockEmailStatusAnalyzer, $mockEmailStatusManager);

        $resource->beforeSendPerformed($mockEvent);
        $resource->sendPerformed($mockEvent);
    }

    /**
     * @noRector \Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector
     * @return never
     */
    public function testBeforeSendPerformedWithGetBccRecipients(): void
    {
        $mockEmailStatusAnalyzer = $this->createMock(EmailStatusAnalyzer::class);
        $mockEmailStatusManager  = $this->createMock(EmailStatusManager::class);
        $mockEvent               = $this->createMock(\Swift_Events_SendEvent::class);
        $mockMessage             = $this->getMessageWithRecipients();

        $mockEmailStatusAnalyzer->expects(self::exactly(5))->method('canReceiveMessages')->willReturnCallback([$this, 'canReceiveMessages']);
        $mockEmailStatusManager->expects(self::exactly(5))->method('loadEmailStatus')->willReturnMap($this->getEmailStatusMap());
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($mockMessage);
        $mockEvent->expects(self::once())->method('setFailedRecipients')->willReturnCallback([$this, 'confirmAllButSuccessAreBlacklisted']);
        $mockMessage->expects(self::exactly(1))->method('getTo')->willReturn(null);
        $mockMessage->expects(self::exactly(1))->method('getCc')->willReturn(null);
        $mockMessage->expects(self::exactly(2))->method('getBcc')->willReturn($this->getRecipients());
        $mockMessage->expects(self::never())->method('setTo')->willReturnCallback([$this, 'confirmAllButSuccessAreRemoved']);
        $mockMessage->expects(self::never())->method('setCc');
        $mockMessage->expects(self::once())->method('setBcc');

        $resource = new MonitorFilterPlugin($mockEmailStatusAnalyzer, $mockEmailStatusManager);

        $resource->beforeSendPerformed($mockEvent);
        $resource->sendPerformed($mockEvent);
    }

    public function canReceiveMessages(): bool
    {
        /** @var EmailStatus $emailStatus */
        $emailStatus = \func_get_arg(0);

        switch ($emailStatus->getAddress()) {
            case 'bounced@example.com':
                return false;
            case 'complained@example.com':
                return false;
            case 'ooto@example.com':
                return false;
            case 'success@example.com':
                return true;
            case 'suppressed@example.com':
                return false;
        }

        throw new \RuntimeException('The email is not found: maybe you wrote it wrong in the tests.');
    }

    public function confirmAllButSuccessAreBlacklisted(): void
    {
        $recipients = \func_get_arg(0);

        self::assertContains('bounced@example.com', $recipients);
        self::assertContains('complained@example.com', $recipients);
        self::assertContains('ooto@example.com', $recipients);
        self::assertNotContains('success@example.com', $recipients);
        self::assertContains('suppressed@example.com', $recipients);
    }

    public function confirmAllButSuccessAreRemoved(): void
    {
        $recipients = \func_get_arg(0);
        self::assertArrayNotHasKey('bounced@example.com', $recipients);
        self::assertArrayNotHasKey('complained@example.com', $recipients);
        self::assertArrayNotHasKey('ooto@example.com', $recipients);
        self::assertArrayHasKey('success@example.com', $recipients);
        self::assertArrayNotHasKey('suppressed@example.com', $recipients);
    }

    /**
     * @return MockObject|\Swift_Message
     */
    private function getMessageWithRecipients()
    {
        $message = $this->createMock(\Swift_Message::class);
        $message->expects(self::once())->method('getFrom')->willReturn(['hello@serendipityhq.com']);

        return $message;
    }

    private function getRecipients(): array
    {
        return $recipients = ['bounced@example.com' => null, 'complained@example.com' => null, 'ooto@example.com' => null, 'success@example.com' => null, 'suppressed@example.com' => null];
    }

    /**
     * Configures the EmailStatusManager to return the mocked EmailStatus objects each
     * time the EmailStatusManager::loadOrCreateEmailStatus() method is called.
     */
    private function getEmailStatusMap(): array
    {
        $mockBouncedEmailStatus = $this->createMock(EmailStatus::class);
        $mockBouncedEmailStatus->method('getAddress')->willReturn('bounced@example.com');
        $mockBouncedEmailStatus->method('getHardBouncesCount')->willReturn(3);
        $mockBouncedEmailStatus->method('getSoftBouncesCount')->willReturn(3);

        $mockComplainedCollection = $this->getMockBuilder(ArrayCollection::class)->getMock();
        $mockComplainedCollection->method('count')->willReturn(1);
        $mockComplainedEmailStatus = $this->createMock(EmailStatus::class);
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
