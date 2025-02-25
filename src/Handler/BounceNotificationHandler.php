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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles notifications of bounced Emails.
 * {@inheritdoc}
 */
final class BounceNotificationHandler extends AbstractNotification
{
    public function processNotification(array $notification, MailMessage $mailMessage): Response
    {
        foreach ($notification['bounce']['bouncedRecipients'] as $bouncedRecipient) {
            $email = $this->getEmailStatusManager()->loadOrCreateEmailStatus($bouncedRecipient['emailAddress']);

            Bounce::create($email, $mailMessage, $bouncedRecipient, $notification);
        }

        return new Response('', Response::HTTP_OK);
    }
}
