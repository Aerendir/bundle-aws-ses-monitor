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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Util;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\Monitor;

/**
 * Helps to analyze an EmailStatus to understand if it is bounced, complained or healthy.
 */
class EmailStatusAnalyzer
{
    /** @var Monitor $monitor */
    private $monitor;

    /**
     * @param Monitor $monitor
     */
    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * @param EmailStatus $emailStatus
     * @param string      $identity
     *
     * @return bool
     */
    public function canReceiveMessages(EmailStatus $emailStatus, string $identity): bool
    {
        $filter     = $this->monitor->findConfiguredIdentity($identity, 'bounces')['filter'];
        $maxBounces = $filter['max_bounces'];
        $softAsHard = $filter['soft_as_hard'];

        // If this email is bounces
        if ($this->isBounced($emailStatus, $maxBounces, $softAsHard)) {
            // But bounced emails tracking is disabled
            if (false === $this->monitor->bouncesTrackingIsEnabled($identity)) {
                return true;
            }

            // Or anyway the sending to bounced emails is forced
            if (true === $this->monitor->bouncesSendingIsForced($identity)) {
                return true;
            }

            // This email is bounced and cannot receive messages
            return false;
        }
        // If this email is complained
        elseif ($this->isComplained($emailStatus)) {
            // But complained emails tracking is disabled
            if (false === $this->monitor->complaintsTrackingIsEnabled($identity)) {
                return true;
            }

            // Or anyway the sending to complained emails is forced
            if (true === $this->monitor->complaintsSendingIsForced($identity)) {
                return true;
            }

            // This email is complained and cannot receive messages
            return false;
        }

        // This email is not bounced nor complained: can receive messages
        return true;
    }

    /**
     * @param EmailStatus $emailStatus
     * @param int         $maxBounces
     * @param bool        $softAsHard
     *
     * @return bool
     */
    public function isBounced(EmailStatus $emailStatus, int $maxBounces, bool $softAsHard = false): bool
    {
        $bouncesCount = $emailStatus->getHardBouncesCount();

        if ($softAsHard) {
            $bouncesCount += $emailStatus->getSoftBouncesCount();
        }

        if ($bouncesCount >= $maxBounces) {
            return true;
        }

        return false;
    }

    /**
     * @param EmailStatus $emailStatus
     * @param int         $maxComplained
     *
     * @return bool
     */
    public function isComplained(EmailStatus $emailStatus, int $maxComplained = 1): bool
    {
        return $emailStatus->getComplaints()->count() >= $maxComplained;
    }
}
