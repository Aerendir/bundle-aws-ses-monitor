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

use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the confirmation of the subscription.
 */
final class SubscriptionProcessor
{
    private SnsClient $snsClient;

    private EntityManagerInterface $entityManager;

    private MessageHelper $messageHelper;

    public function __construct(SnsClient $snsClient, EntityManagerInterface $entityManager, MessageHelper $messageHelper)
    {
        $this->snsClient     = $snsClient;
        $this->entityManager = $entityManager;
        $this->messageHelper = $messageHelper;
    }

    public function processRequest(Request $request): Response
    {
        $message = $this->messageHelper->buildMessageFromRequest($request);

        if (false === $this->messageHelper->validateNotification($message)) {
            return new Response('The message is invalid.', 403);
        }

        $topic = $this->entityManager->getRepository(Topic::class)->findOneBy(['arn' => $message->offsetGet('TopicArn')]);

        if ( ! $topic instanceof Topic) {
            return new Response('Topic not found', 404);
        }

        $this->snsClient->confirmSubscription(
            [
                'TopicArn' => $topic->getArn(),
                'Token'    => $message->offsetGet('Token'),
            ]
        );

        $this->entityManager->flush();

        return new Response('OK', 200);
    }
}
