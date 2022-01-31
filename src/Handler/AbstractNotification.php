<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Common constructor to all notification handlers.
 */
abstract class AbstractNotification
{
    /** @var EmailStatusManager $emailStatusManager */
    private $emailStatusManager;

    public function __construct(EmailStatusManager $emailStatusManager)
    {
        $this->emailStatusManager = $emailStatusManager;
    }

    abstract public function processNotification(array $notification, MailMessage $mailMessage): Response;

    protected function getEmailStatusManager(): EmailStatusManager
    {
        return $this->emailStatusManager;
    }
}
