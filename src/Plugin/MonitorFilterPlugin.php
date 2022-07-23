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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\EmailStatusAnalyzer;

/**
 * The SwiftMailer plugin.
 * {@inheritdoc}
 */
final class MonitorFilterPlugin implements \Swift_Events_SendListener
{
    private array $blacklisted;
    private EmailStatusAnalyzer $emailStatusAnalyzer;
    private EmailStatusManager $emailStatusManager;

    public function __construct(EmailStatusAnalyzer $emailStatusAnalyzer, EmailStatusManager $emailStatusManager)
    {
        $this->emailStatusAnalyzer = $emailStatusAnalyzer;
        $this->emailStatusManager  = $emailStatusManager;
    }

    /**
     * Invoked immediately before the MailMessage is sent.
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $event): void
    {
        // Reset the blacklisted array
        $this->blacklisted = [];
        $message           = $event->getMessage();
        $identities        = $message->getFrom();

        if (null !== $message->getTo()) {
            $message->setTo($this->filterBlacklisted($identities, $message->getTo()));
        }

        if (null !== $message->getCc()) {
            $message->setCc($this->filterBlacklisted($identities, $message->getCc()));
        }

        if (null !== $message->getBcc()) {
            $message->setBcc($this->filterBlacklisted($identities, $message->getBcc()));
        }
    }

    /**
     * Invoked immediately after the MailMessage is sent.
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt): void
    {
        $evt->setFailedRecipients(\array_unique($this->blacklisted));
    }

    private function filterBlacklisted(array $identities, array $recipients): array
    {
        $emails         = \array_keys($recipients);
        $fromIdentities = \array_keys($identities);

        foreach ($emails as $email) {
            $emailStatus = $this->emailStatusManager->loadEmailStatus($email);

            foreach ($fromIdentities as $identity) {
                if (null !== $emailStatus && false === $this->emailStatusAnalyzer->canReceiveMessages($emailStatus, (string) $identity)) {
                    $this->blacklisted[] = $emailStatus->getAddress();
                    unset($recipients[$emailStatus->getAddress()]);
                }
            }
        }

        return $recipients;
    }
}
