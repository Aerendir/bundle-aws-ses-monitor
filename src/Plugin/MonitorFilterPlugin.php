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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\EmailStatusRepository;
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

    /** @var EmailStatusRepository $emailStatusRepo */
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
    public function beforeSendPerformed(Swift_Events_SendEvent $event)
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
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $evt->setFailedRecipients(array_keys($this->blacklisted));
    }

    /**
     * @param $recipients
     *
     * @return mixed
     */
    private function filterBlacklisted($recipients)
    {
        if ( ! is_array($recipients)) {
            return $recipients;
        }

        $emails = array_keys($recipients);

        foreach ($emails as $email) {
            $email = $this->emailStatusRepo->findOneByEmailAddress($email);

            if (null !== $email && ($this->isBounced($email) || $this->isComplained($email))) {
                $this->blacklisted[$email->getEmailAddress()] = $recipients[$email->getEmailAddress()];
                unset($recipients[$email->getEmailAddress()]);
            }
        }

        return $recipients;
    }

    /**
     * @param EmailStatus $email
     *
     * @return bool
     */
    private function isBounced(EmailStatus $email)
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
    private function isComplained(EmailStatus $email)
    {
        if (false === $this->areComplaintsChecksEnabled()) {
            return false;
        }

        if (true === $this->areComplaintsForced()) {
            return false;
        }

        if ($email->getComplaintsCount() >= 1) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function areBouncesChecksEnabled()
    {
        return $this->bouncesConfig['enabled'];
    }

    /**
     * @return bool
     */
    private function areBouncesForced()
    {
        return $this->bouncesConfig['force_send'];
    }

    /**
     * @return bool
     */
    private function areComplaintsChecksEnabled()
    {
        return $this->complaintsConfig['enabled'];
    }

    /**
     * @return bool
     */
    private function areComplaintsForced()
    {
        return $this->complaintsConfig['force_send'];
    }
}
