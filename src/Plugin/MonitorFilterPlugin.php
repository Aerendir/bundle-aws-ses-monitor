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

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use Swift_Events_SendEvent;

/**
 * The SwiftMailer plugin.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class MonitorFilterPlugin implements \Swift_Events_SendListener
{
    /** @var array $blacklisted */
    private $blacklisted = [];

    /** @var array $bouncesConfig */
    private $bouncesConfig;

    /** @var array $complaintsConfig */
    private $complaintsConfig;

    /** @var \Doctrine\Common\Persistence\ObjectRepository $emailStatusRepo */
    private $emailStatusRepo;

    /**
     * @param EntityManagerInterface $manager
     * @param array                  $bouncesConfig    The configuration of bounces
     * @param array                  $complaintsConfig The configuration of complaints
     */
    public function __construct(EntityManagerInterface $manager, array $bouncesConfig, array $complaintsConfig)
    {
        $this->bouncesConfig    = $bouncesConfig['filter'];
        $this->complaintsConfig = $complaintsConfig['filter'];
        $this->emailStatusRepo  = $manager->getRepository(EmailStatus::class);
    }

    /**
     * Invoked immediately before the MailMessage is sent.
     *
     * @param Swift_Events_SendEvent $event
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event): void
    {
        $message = $event->getMessage();

        $message->setTo($this->filterBlacklisted($message->getTo()));
        $message->setCc($this->filterBlacklisted($message->getCc()));
        $message->setBcc($this->filterBlacklisted($message->getBcc()));
    }

    /**
     * Invoked immediately after the MailMessage is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt): void
    {
        $evt->setFailedRecipients(array_keys($this->blacklisted));
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
            /** @var EmailStatus|null $email */
            $email = $this->emailStatusRepo->findOneBy(['email' => $email]);

            if (null !== $email && ($this->isBounced($email) || $this->isComplained($email))) {
                $this->blacklisted[$email->getAddress()] = $recipients[$email->getAddress()];
                unset($recipients[$email->getAddress()]);
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
