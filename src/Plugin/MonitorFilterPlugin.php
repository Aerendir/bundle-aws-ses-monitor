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

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use Swift_Events_SendEvent;

/**
 * The SwiftMailer plugin.
 * {@inheritdoc}
 */
class MonitorFilterPlugin implements \Swift_Events_SendListener
{
    /** @var array $blacklisted */
    private $blacklisted;

    /** @var array $bouncesConfig */
    private $bouncesConfig;

    /** @var array $complaintsConfig */
    private $complaintsConfig;

    /** @var EmailStatusManager $emailStatusManager */
    private $emailStatusManager;

    /**
     * @param EmailStatusManager $emailStatusManager
     * @param array              $bouncesConfig      The configuration of bounces
     * @param array              $complaintsConfig   The configuration of complaints
     */
    public function __construct(EmailStatusManager $emailStatusManager, array $bouncesConfig, array $complaintsConfig)
    {
        $this->bouncesConfig      = $bouncesConfig['filter'];
        $this->complaintsConfig   = $complaintsConfig['filter'];
        $this->emailStatusManager = $emailStatusManager;
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

        if (null !== $message->getTo()) {
            $message->setTo($this->filterBlacklisted($message->getTo()));
        }

        if (null !== $message->getCc()) {
            $message->setCc($this->filterBlacklisted($message->getCc()));
        }

        if (null !== $message->getBcc()) {
            $message->setBcc($this->filterBlacklisted($message->getBcc()));
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
     * @param array $recipients
     *
     * @return array
     */
    private function filterBlacklisted(array $recipients): array
    {
        $emails = array_keys($recipients);

        foreach ($emails as $email) {
            $emailStatus = $this->emailStatusManager->loadEmailStatus($email);

            if (null !== $emailStatus && ($this->isBounced($emailStatus) || $this->isComplained($emailStatus))) {
                $this->blacklisted[] = $emailStatus->getAddress();
                unset($recipients[$emailStatus->getAddress()]);
            }
        }

        return $recipients;
    }

    /**
     * @param EmailStatus $email
     *
     * @return bool
     */
    private function isBounced(EmailStatus $email): bool
    {
        if (false === $this->areBouncesChecksEnabled()) {
            return false;
        }

        if (true === $this->areBouncesForced()) {
            return false;
        }

        $bouncesCount = $email->getHardBouncesCount();

        if ($this->bouncesConfig['soft_as_hard']) {
            $bouncesCount += $email->getSoftBouncesCount();
        }

        if ($bouncesCount >= $this->bouncesConfig['max_bounces']) {
            return true;
        }

        return false;
    }

    /**
     * @param EmailStatus $email
     *
     * @return bool
     */
    private function isComplained(EmailStatus $email): bool
    {
        if (false === $this->areComplaintsChecksEnabled()) {
            return false;
        }

        if (true === $this->areComplaintsForced()) {
            return false;
        }

        if ($email->getComplaints()->count() >= 1) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function areBouncesChecksEnabled(): bool
    {
        return $this->bouncesConfig['enabled'];
    }

    /**
     * @return bool
     */
    private function areBouncesForced(): bool
    {
        return $this->bouncesConfig['force_send'];
    }

    /**
     * @return bool
     */
    private function areComplaintsChecksEnabled(): bool
    {
        return $this->complaintsConfig['enabled'];
    }

    /**
     * @return bool
     */
    private function areComplaintsForced(): bool
    {
        return $this->complaintsConfig['force_send'];
    }
}
