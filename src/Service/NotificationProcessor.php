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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

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
class NotificationProcessor
{
    /** @var BounceNotificationHandler $bounceNotificationHandler */
    private $bounceNotificationHandler;

    /** @var ComplaintNotificationHandler $complaintNotificationHandler */
    private $complaintNotificationHandler;

    /** @var DeliveryNotificationHandler $deliveryNotificationHandler */
    private $deliveryNotificationHandler;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var MessageHelper $messageHelper */
    private $messageHelper;

    /**
     * @param BounceNotificationHandler    $bounceNotificationHandler
     * @param ComplaintNotificationHandler $complaintNotificationHandler
     * @param DeliveryNotificationHandler  $deliveryNotificationHandler
     * @param EntityManagerInterface       $entityManager
     * @param MessageHelper                $messageHelper
     */
    public function __construct(
        BounceNotificationHandler $bounceNotificationHandler,
        ComplaintNotificationHandler $complaintNotificationHandler,
        DeliveryNotificationHandler $deliveryNotificationHandler,
        EntityManagerInterface $entityManager,
        MessageHelper $messageHelper
    ) {
        $this->bounceNotificationHandler       = $bounceNotificationHandler;
        $this->complaintNotificationHandler    = $complaintNotificationHandler;
        $this->deliveryNotificationHandler     = $deliveryNotificationHandler;
        $this->entityManager                   = $entityManager;
        $this->messageHelper                   = $messageHelper;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function processRequest(Request $request): Response
    {
        $message = $this->messageHelper->buildMessageFromRequest($request);

        if (false === $this->messageHelper->validateNotification($message)) {
            return new Response('The message is invalid.', 403);
        }

        $notificationData = $this->messageHelper->extractMessageData($message);

        if (false === isset($notificationData['notificationType'])) {
            return new Response('Missed NotificationType.', 403);
        }

        if (SnsTypes::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS === $notificationData['notificationType']) {
            return new Response('OK', 200);
        }

        $mailMessage = $this->loadOrCreateMailMessage($notificationData);
        switch ($notificationData['notificationType']) {
            case SnsTypes::MESSAGE_TYPE_BOUNCE:
                return $this->bounceNotificationHandler->processNotification($notificationData, $mailMessage);
                break;
            case SnsTypes::MESSAGE_TYPE_COMPLAINT:
                return $this->complaintNotificationHandler->processNotification($notificationData, $mailMessage);
                break;
            case SnsTypes::MESSAGE_TYPE_DELIVERY:
                return $this->deliveryNotificationHandler->processNotification($notificationData, $mailMessage);
                break;
            default:
                return new Response('Notification type not understood', 403);
        }
    }

    /**
     * @param array $messageData
     *
     * @return MailMessage
     */
    private function loadOrCreateMailMessage(array $messageData): MailMessage
    {
        $mailMessageData = $messageData['mail'];

        /** @var MailMessage|null $mailMessage */
        $mailMessage     = $this->entityManager->getRepository(MailMessage::class)->findOneBy(['messageId' => $mailMessageData['messageId']]);

        // If a MailMessage object doesn't already exists...
        if (null === $mailMessage) {
            // Create it
            $mailMessage = MailMessage::create($mailMessageData);
            $this->entityManager->persist($mailMessage);
        }

        return $mailMessage;
    }
}
