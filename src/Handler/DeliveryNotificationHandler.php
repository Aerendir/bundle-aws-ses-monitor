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

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles notifications of delivered emails.
 * {@inheritdoc}
 */
final class DeliveryNotificationHandler extends AbstractNotification
{
    public function processNotification(array $notification, MailMessage $mailMessage): Response
    {
        foreach ($notification['delivery']['recipients'] as $recipient) {
            $email = $this->getEmailStatusManager()->loadOrCreateEmailStatus($recipient);

            Delivery::create($email, $mailMessage, $notification);
        }

        return new Response('', 200);
    }
}
