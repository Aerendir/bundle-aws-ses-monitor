<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\BounceNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\ComplaintNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\DeliveryNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Processes the request from AWS SNS handling it with the right handler.
 */
final class NotificationProcessor
{
    /**
     * @var string
     */
    private const NOTIFICATION_TYPE = 'notificationType';

    private BounceNotificationHandler $bounceNotificationHandler;

    private ComplaintNotificationHandler $complaintNotificationHandler;

    private DeliveryNotificationHandler $deliveryNotificationHandler;

    private EntityManagerInterface $entityManager;

    private MessageHelper $messageHelper;

    public function __construct(
        BounceNotificationHandler $bounceNotificationHandler,
        ComplaintNotificationHandler $complaintNotificationHandler,
        DeliveryNotificationHandler $deliveryNotificationHandler,
        EntityManagerInterface $entityManager,
        MessageHelper $messageHelper
    ) {
        $this->bounceNotificationHandler    = $bounceNotificationHandler;
        $this->complaintNotificationHandler = $complaintNotificationHandler;
        $this->deliveryNotificationHandler  = $deliveryNotificationHandler;
        $this->entityManager                = $entityManager;
        $this->messageHelper                = $messageHelper;
    }

    public function processRequest(Request $request): Response
    {
        $message = $this->messageHelper->buildMessageFromRequest($request);

        if (false === $this->messageHelper->validateNotification($message)) {
            return new Response('The message is invalid.', 403);
        }

        $notificationData = $this->messageHelper->extractMessageData($message);

        if (false === isset($notificationData[self::NOTIFICATION_TYPE])) {
            return new Response('Missed NotificationType.', 403);
        }

        if (SnsTypes::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS === $notificationData[self::NOTIFICATION_TYPE]) {
            return new Response('OK', 200);
        }

        $mailMessage = $this->loadOrCreateMailMessage($notificationData);
        switch ($notificationData[self::NOTIFICATION_TYPE]) {
            case SnsTypes::MESSAGE_TYPE_BOUNCE:
                return $this->bounceNotificationHandler->processNotification($notificationData, $mailMessage);
            case SnsTypes::MESSAGE_TYPE_COMPLAINT:
                return $this->complaintNotificationHandler->processNotification($notificationData, $mailMessage);
            case SnsTypes::MESSAGE_TYPE_DELIVERY:
                return $this->deliveryNotificationHandler->processNotification($notificationData, $mailMessage);
            default:
                return new Response('Notification type not understood', 403);
        }
    }

    private function loadOrCreateMailMessage(array $messageData): MailMessage
    {
        $mailMessageData = $messageData['mail'];

        $mailMessage = $this->entityManager->getRepository(MailMessage::class)->findOneBy(['messageId' => $mailMessageData['messageId']]);

        // If a MailMessage object doesn't already exists...
        if ( ! $mailMessage instanceof MailMessage) {
            // Create it
            $mailMessage = MailMessage::create($mailMessageData);
            $this->entityManager->persist($mailMessage);
        }

        return $mailMessage;
    }
}
