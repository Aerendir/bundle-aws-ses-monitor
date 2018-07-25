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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\IdentitiesStore;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\EmailStatusAnalyzer;
use Swift_Events_SendEvent;

/**
 * The SwiftMailer plugin.
 * {@inheritdoc}
 */
class MonitorFilterPlugin implements \Swift_Events_SendListener
{
    /** @var array $blacklisted */
    private $blacklisted;

    /** @var EmailStatusAnalyzer $emailStatusAnalyzer */
    private $emailStatusAnalyzer;

    /** @var EmailStatusManager $emailStatusManager */
    private $emailStatusManager;

    /** @var IdentitiesStore $identities */
    private $identities;

    /**
     * @param EmailStatusAnalyzer $emailStatusAnalyzer
     * @param EmailStatusManager  $emailStatusManager
     * @param IdentitiesStore     $identitiesStore
     */
    public function __construct(EmailStatusAnalyzer $emailStatusAnalyzer, EmailStatusManager $emailStatusManager, IdentitiesStore $identitiesStore)
    {
        $this->emailStatusAnalyzer = $emailStatusAnalyzer;
        $this->emailStatusManager  = $emailStatusManager;
        $this->identities          = $identitiesStore;
    }

    /**
     * Invoked immediately before the MailMessage is sent.
     *
     * @param Swift_Events_SendEvent $event
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event): void
    {
        // Reset the blacklisted array
        $this->blacklisted = [];
        $message           = $event->getMessage();
        $identity          = $message->getFrom();

        if (null !== $message->getTo()) {
            $message->setTo($this->filterBlacklisted($identity, $message->getTo()));
        }

        if (null !== $message->getCc()) {
            $message->setCc($this->filterBlacklisted($identity, $message->getCc()));
        }

        if (null !== $message->getBcc()) {
            $message->setBcc($this->filterBlacklisted($identity, $message->getBcc()));
        }
    }

    /**
     * Invoked immediately after the MailMessage is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt): void
    {
        $evt->setFailedRecipients(array_unique($this->blacklisted));
    }

    /**
     * @param string $identity
     * @param array  $recipients
     *
     * @return array
     */
    private function filterBlacklisted(string $identity, array $recipients): array
    {
        $emails = array_keys($recipients);

        foreach ($emails as $email) {
            $emailStatus = $this->emailStatusManager->loadEmailStatus($email);

            if (null !== $emailStatus && false === $this->emailStatusAnalyzer->canReceiveMessages($emailStatus, $identity)) {
                $this->blacklisted[] = $emailStatus->getAddress();
                unset($recipients[$emailStatus->getAddress()]);
            }
        }

        return $recipients;
    }
}
