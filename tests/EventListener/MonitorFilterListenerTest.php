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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\EventListener\MonitorFilterListener;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\EmailStatusAnalyzer;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

final class MonitorFilterListenerTest extends TestCase
{
    private const FROM = 'hello@serendipityhq.com';

    /** The recipients that cannot receive messages any more */
    private const BLACKLISTED = ['bounced@example.com', 'complained@example.com', 'ooto@example.com', 'suppressed@example.com'];

    /** The recipient that can receive messages */
    private const SUCCESS = 'success@example.com';

    /** A recipient that has no status at all, as nothing was ever received about it */
    private const UNKNOWN = 'unknown@example.com';

    public function testItSubscribesToTheMessageEvent(): void
    {
        self::assertSame([MessageEvent::class => 'onMessage'], MonitorFilterListener::getSubscribedEvents());
    }

    public function testItRemovesTheBlacklistedRecipientsFromHeadersAndEnvelope(): void
    {
        $email = (new Email())
            ->from(self::FROM)
            ->to('bounced@example.com', self::SUCCESS)
            ->cc('complained@example.com', self::UNKNOWN)
            ->bcc('ooto@example.com', 'suppressed@example.com', self::SUCCESS);
        $event = new MessageEvent($email, Envelope::create($email), 'main');

        $this->createListener()->onMessage($event);

        self::assertFalse($event->isRejected());
        self::assertSame([self::SUCCESS], $this->addresses($email->getTo()));
        self::assertSame([self::UNKNOWN], $this->addresses($email->getCc()));
        self::assertSame([self::SUCCESS], $this->addresses($email->getBcc()));
        self::assertSame([self::SUCCESS, self::UNKNOWN, self::SUCCESS], $this->addresses($event->getEnvelope()->getRecipients()));
    }

    public function testItRemovesTheHeadersLeftWithoutRecipients(): void
    {
        $email = (new Email())
            ->from(self::FROM)
            ->to(self::SUCCESS)
            ->cc('complained@example.com')
            ->bcc('bounced@example.com');
        $event = new MessageEvent($email, Envelope::create($email), 'main');

        $this->createListener()->onMessage($event);

        self::assertFalse($event->isRejected());
        self::assertSame([self::SUCCESS], $this->addresses($email->getTo()));
        self::assertFalse($email->getHeaders()->has('Cc'));
        self::assertFalse($email->getHeaders()->has('Bcc'));
        self::assertSame([self::SUCCESS], $this->addresses($event->getEnvelope()->getRecipients()));
    }

    public function testItRejectsTheMessageWhenNoRecipientIsLeft(): void
    {
        $email = (new Email())
            ->from(self::FROM)
            ->to('bounced@example.com')
            ->cc('complained@example.com');
        $event = new MessageEvent($email, Envelope::create($email), 'main');

        $this->createListener()->onMessage($event);

        self::assertTrue($event->isRejected());
        self::assertFalse($email->getHeaders()->has('To'));
        self::assertFalse($email->getHeaders()->has('Cc'));
    }

    public function testItFiltersTheRecipientsSetExplicitlyOnTheEnvelope(): void
    {
        $email    = (new Email())->from(self::FROM)->to(self::SUCCESS);
        $envelope = new Envelope(new Address(self::FROM), [new Address('bounced@example.com'), new Address(self::UNKNOWN)]);
        $event    = new MessageEvent($email, $envelope, 'main');

        $this->createListener()->onMessage($event);

        self::assertFalse($event->isRejected());
        self::assertSame([self::UNKNOWN], $this->addresses($event->getEnvelope()->getRecipients()));
        self::assertSame([self::SUCCESS], $this->addresses($email->getTo()));
    }

    public function testARecipientIsBlacklistedWhenAnyOfTheIdentitiesCannotSendToIt(): void
    {
        $analyzer = $this->createMock(EmailStatusAnalyzer::class);
        $analyzer->method('canReceiveMessages')->willReturnCallback(
            // Only the second identity cannot send to the recipient
            static fn (EmailStatus $emailStatus, string $identity): bool => 'other@serendipityhq.com' !== $identity
        );

        $email = (new Email())->from(self::FROM, 'other@serendipityhq.com')->to(self::SUCCESS);
        $event = new MessageEvent($email, Envelope::create($email), 'main');

        (new MonitorFilterListener($analyzer, $this->createEmailStatusManager()))->onMessage($event);

        self::assertTrue($event->isRejected());
    }

    public function testItUsesTheSenderOfTheEnvelopeWhenTheMessageHasNoHeaders(): void
    {
        $analyzer = $this->createMock(EmailStatusAnalyzer::class);
        $analyzer->expects(self::exactly(2))->method('canReceiveMessages')->willReturnCallback(
            function (EmailStatus $emailStatus, string $identity): bool {
                self::assertSame('sender@serendipityhq.com', $identity);

                return self::SUCCESS === $emailStatus->getAddress();
            }
        );

        $envelope = new Envelope(new Address('sender@serendipityhq.com'), [new Address('bounced@example.com'), new Address(self::SUCCESS)]);
        $event    = new MessageEvent(new RawMessage('A raw message'), $envelope, 'main');

        (new MonitorFilterListener($analyzer, $this->createEmailStatusManager()))->onMessage($event);

        self::assertFalse($event->isRejected());
        self::assertSame([self::SUCCESS], $this->addresses($event->getEnvelope()->getRecipients()));
    }

    public function testItLoadsTheStatusOfEachRecipientOnlyOnce(): void
    {
        $manager = $this->createMock(EmailStatusManager::class);
        $manager->expects(self::exactly(2))->method('loadEmailStatus')->willReturnCallback(fn (string $email): ?EmailStatus => $this->createEmailStatus($email));

        $email = (new Email())->from(self::FROM)->to('bounced@example.com', self::SUCCESS)->bcc(self::SUCCESS);
        $event = new MessageEvent($email, Envelope::create($email), 'main');

        (new MonitorFilterListener($this->createEmailStatusAnalyzer(), $manager))->onMessage($event);

        self::assertSame([self::SUCCESS], $this->addresses($email->getTo()));
        self::assertSame([self::SUCCESS], $this->addresses($email->getBcc()));
    }

    public function testItIgnoresTheMessagesWhenTheyAreQueued(): void
    {
        $manager = $this->createMock(EmailStatusManager::class);
        $manager->expects(self::never())->method('loadEmailStatus');

        $email = (new Email())->from(self::FROM)->to('bounced@example.com');
        $event = new MessageEvent($email, Envelope::create($email), 'main', true);

        (new MonitorFilterListener($this->createEmailStatusAnalyzer(), $manager))->onMessage($event);

        self::assertFalse($event->isRejected());
        self::assertSame(['bounced@example.com'], $this->addresses($email->getTo()));
    }

    public function testItFiltersTheMessagesOfEveryTransport(): void
    {
        $listener = $this->createListener();

        foreach (['smtps://email-smtp.eu-west-1.amazonaws.com:465', 'ses+api://eu-west-1', 'null://'] as $transport) {
            $email = (new Email())->from(self::FROM)->to('bounced@example.com', self::SUCCESS);
            $listener->onMessage(new MessageEvent($email, Envelope::create($email), $transport));

            self::assertSame([self::SUCCESS], $this->addresses($email->getTo()), $transport);
        }
    }

    public function testTheBlacklistOfAMessageDoesNotLeakIntoTheNextOne(): void
    {
        $canReceive = false;
        $analyzer   = $this->createMock(EmailStatusAnalyzer::class);
        $analyzer->method('canReceiveMessages')->willReturnCallback(static function () use (&$canReceive): bool {
            return $canReceive;
        });
        $listener = new MonitorFilterListener($analyzer, $this->createEmailStatusManager());

        $first      = (new Email())->from(self::FROM)->to('bounced@example.com', self::UNKNOWN);
        $listener->onMessage(new MessageEvent($first, Envelope::create($first), 'main'));
        self::assertSame([self::UNKNOWN], $this->addresses($first->getTo()));

        // In the meantime the recipient became able to receive messages again
        $canReceive = true;
        $second     = (new Email())->from(self::FROM)->to('bounced@example.com');
        $event      = new MessageEvent($second, Envelope::create($second), 'main');
        $listener->onMessage($event);

        self::assertFalse($event->isRejected());
        self::assertSame(['bounced@example.com'], $this->addresses($second->getTo()));
    }

    private function createListener(): MonitorFilterListener
    {
        return new MonitorFilterListener($this->createEmailStatusAnalyzer(), $this->createEmailStatusManager());
    }

    private function createEmailStatusAnalyzer(): EmailStatusAnalyzer
    {
        $analyzer = $this->createMock(EmailStatusAnalyzer::class);
        $analyzer->method('canReceiveMessages')->willReturnCallback(
            static fn (EmailStatus $emailStatus, string $identity): bool => false === \in_array($emailStatus->getAddress(), self::BLACKLISTED, true)
        );

        return $analyzer;
    }

    private function createEmailStatusManager(): EmailStatusManager
    {
        $manager = $this->createMock(EmailStatusManager::class);
        $manager->method('loadEmailStatus')->willReturnCallback(fn (string $email): ?EmailStatus => $this->createEmailStatus($email));

        return $manager;
    }

    private function createEmailStatus(string $email): ?EmailStatus
    {
        if (self::UNKNOWN === $email) {
            return null;
        }

        $emailStatus = $this->createMock(EmailStatus::class);
        $emailStatus->method('getAddress')->willReturn($email);

        return $emailStatus;
    }

    /**
     * @param Address[] $addresses
     *
     * @return string[]
     */
    private function addresses(array $addresses): array
    {
        return \array_map(static fn (Address $address): string => $address->getAddress(), $addresses);
    }
}
