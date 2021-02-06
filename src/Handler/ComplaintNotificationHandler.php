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

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles notifications of compalined emails.
 * {@inheritdoc}
 */
final class ComplaintNotificationHandler extends AbstractNotification
{
    /**
     * @param array       $notification
     * @param MailMessage $mailMessage
     *
     * @return Response
     */
    public function processNotification(array $notification, MailMessage $mailMessage): Response
    {
        foreach ($notification['complaint']['complainedRecipients'] as $complainedRecipient) {
            $email = $this->getEmailStatusManager()->loadOrCreateEmailStatus($complainedRecipient['emailAddress']);

            Complaint::create($email, $mailMessage, $notification);
        }

        return new Response('', 200);
    }
}
