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

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles notifications of compalined emails.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class ComplaintNotificationHandler extends AbstractNotification
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
            $email = $this->getEmailManager()->loadOrCreateEmail($complainedRecipient['emailAddress']);

            Complaint::create($email, $mailMessage, $notification);
        }

        return new Response('', 200);
    }
}
