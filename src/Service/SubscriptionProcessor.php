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

use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Factory\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the confirmation of the subscription.
 */
class SubscriptionProcessor
{
    /** @var SnsClient $snsClient */
    private $snsClient;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var MessageHelper $messageHelper */
    private $messageHelper;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AwsClientFactory       $clientFactory
     * @param MessageHelper          $messageHelper
     */
    public function __construct(AwsClientFactory $clientFactory, EntityManagerInterface $entityManager, MessageHelper $messageHelper)
    {
        $this->snsClient     = $clientFactory->getSnsClient();
        $this->entityManager = $entityManager;
        $this->messageHelper = $messageHelper;
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

        /** @var Topic|null $topic */
        $topic = $this->entityManager->getRepository(Topic::class)->findOneBy(['topicArn' => $message->offsetGet('TopicArn')]);

        if (null === $topic) {
            return new Response('Topic not found', 404);
        }

        $this->snsClient->confirmSubscription(
            [
                'TopicArn' => $topic->getTopicArn(),
                'Token'    => $message->offsetGet('Token'),
            ]
        );

        $this->entityManager->flush();

        return new Response('OK', 200);
    }
}
