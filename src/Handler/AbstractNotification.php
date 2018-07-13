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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Common constructor to all notification handlers.
 */
abstract class AbstractNotification
{
    /** @var EmailManager $emailManager */
    private $emailManager;

    /**
     * @param EmailManager $emailManager
     */
    public function __construct(EmailManager $emailManager)
    {
        $this->emailManager = $emailManager;
    }

    /**
     * @param array       $notification
     * @param MailMessage $mailMessage
     *
     * @return Response
     */
    abstract public function processNotification(array $notification, MailMessage $mailMessage): Response;

    /**
     * @return EmailManager
     */
    public function getEmailManager(): EmailManager
    {
        return $this->emailManager;
    }
}
