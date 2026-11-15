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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\EventListener;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\EmailStatusAnalyzer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Message;

/**
 * Removes the bounced and complained recipients from the messages sent through Symfony Mailer.
 *
 * A recipient is removed when, for at least one of the identities the message is sent from, it cannot receive
 * messages any more, according to the EmailStatusAnalyzer. It is removed both from the envelope, so the
 * transport doesn't deliver to it, and from the To, Cc and Bcc headers, so the remaining recipients don't see it.
 * When no recipient is left, the message is rejected and not sent at all.
 *
 * The messages of every Mailer transport are filtered: the event carries the string form of the transport (its DSN),
 * not the name it has in the configuration, so there is no reliable way to filter only some of them.
 */
final class MonitorFilterListener implements EventSubscriberInterface
{
    /** @var string[] */
    private const RECIPIENT_HEADERS = ['To', 'Cc', 'Bcc'];

    private EmailStatusAnalyzer $emailStatusAnalyzer;
    private EmailStatusManager $emailStatusManager;

    /** @var array<string, bool> */
    private array $blacklisted = [];

    public function __construct(EmailStatusAnalyzer $emailStatusAnalyzer, EmailStatusManager $emailStatusManager)
    {
        $this->emailStatusAnalyzer = $emailStatusAnalyzer;
        $this->emailStatusManager  = $emailStatusManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [MessageEvent::class => 'onMessage'];
    }

    public function onMessage(MessageEvent $event): void
    {
        // When the message is queued (e.g. through Messenger), filter it only when it is actually sent,
        // so the statuses of the recipients are the most recent ones.
        if ($event->isQueued()) {
            return;
        }

        // Reset the blacklisted recipients of the previous message
        $this->blacklisted = [];
        $message           = $event->getMessage();
        $envelope          = $event->getEnvelope();
        $identities        = $this->getIdentities($message, $envelope->getSender());

        if ([] === $identities) {
            return;
        }

        $recipients = \array_values(\array_filter(
            $envelope->getRecipients(),
            fn (Address $recipient): bool => false === $this->isBlacklisted($recipient, $identities)
        ));

        if ($message instanceof Message) {
            $this->filterHeaders($message, $identities);
        }

        if ([] === $recipients) {
            $event->reject();

            return;
        }

        $envelope->setRecipients($recipients);
    }

    /**
     * @return string[]
     */
    private function getIdentities(object $message, Address $sender): array
    {
        if ($message instanceof Message) {
            $from = $message->getHeaders()->get('From');

            if ($from instanceof MailboxListHeader) {
                return \array_map(static fn (Address $address): string => $address->getAddress(), $from->getAddresses());
            }
        }

        return [$sender->getAddress()];
    }

    /**
     * @param string[] $identities
     */
    private function filterHeaders(Message $message, array $identities): void
    {
        $headers = $message->getHeaders();

        foreach (self::RECIPIENT_HEADERS as $name) {
            $header = $headers->get($name);

            if (false === $header instanceof MailboxListHeader) {
                continue;
            }

            $addresses = \array_values(\array_filter(
                $header->getAddresses(),
                fn (Address $recipient): bool => false === $this->isBlacklisted($recipient, $identities)
            ));

            if ([] === $addresses) {
                $headers->remove($name);

                continue;
            }

            $header->setAddresses($addresses);
        }
    }

    /**
     * @param string[] $identities
     */
    private function isBlacklisted(Address $recipient, array $identities): bool
    {
        $email = $recipient->getAddress();

        if (\array_key_exists($email, $this->blacklisted)) {
            return $this->blacklisted[$email];
        }

        $this->blacklisted[$email] = false;
        $emailStatus               = $this->emailStatusManager->loadEmailStatus($email);

        if (null === $emailStatus) {
            return false;
        }

        foreach ($identities as $identity) {
            if (false === $this->emailStatusAnalyzer->canReceiveMessages($emailStatus, $identity)) {
                $this->blacklisted[$email] = true;

                break;
            }
        }

        return $this->blacklisted[$email];
    }
}
