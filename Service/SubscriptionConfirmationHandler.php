<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the confirmation of the subscription.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class SubscriptionConfirmationHandler implements HandlerInterface
{
    const HEADER_TYPE = 'SubscriptionConfirmation';

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var AwsClientFactory $clientFactory */
    private $clientFactory;

    /** @var  MessageValidator */
    private $messageValidator;

    /**
     * @param EntityManager    $entityManager
     * @param AwsClientFactory $clientFactory
     * @param MessageValidator $messageValidator
     */
    public function __construct(EntityManager $entityManager, AwsClientFactory $clientFactory, MessageValidator $messageValidator)
    {
        $this->entityManager    = $entityManager;
        $this->clientFactory    = $clientFactory;
        $this->messageValidator = $messageValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request, Credentials $credentials)
    {
        if (!$request->isMethod('POST')) {
            return 405;
        }

        try {
            $data    = json_decode($request->getContent(), true);
            $message = new Message($data);

            if (false === $this->messageValidator->isValid($message))
                return 403;

        } catch (\Exception $e) {
            return 403;
        }

        if (false === isset($data['Token']) || false === isset($data['TopicArn']))
            return 403;

        $topicArn = $data['TopicArn'];
        $token    = $data['Token'];

        /** @var Topic $topicEntity */
        $topicEntity = $this->entityManager->getRepository('AwsSesMonitorBundle:Topic')->findOneByTopicArn($topicArn);
        if (null === $topicEntity)
            return 403;

        $topicEntity->setToken($token);
        $this->entityManager->persist($topicEntity);

        $client = $this->clientFactory->getSnsClient($credentials);
        $client->confirmSubscription(
            [
                'TopicArn' => $topicEntity->getTopicArn(),
                'Token'    => $topicEntity->getToken()
            ]
        );

        $this->entityManager->flush();

        return 200;
    }
}
