<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * (c) Adamo Aerendir Crespi.
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
 */
class SubscriptionConfirmationHandler implements HandlerInterface
{
    const HEADER_TYPE = 'SubscriptionConfirmation';

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var AwsClientFactory $clientFactory */
    private $clientFactory;

    /**
     * @param EntityManager    $entityManager
     * @param AwsClientFactory $clientFactory
     */
    public function __construct(EntityManager $entityManager, AwsClientFactory $clientFactory)
    {
        $this->entityManager = $entityManager;
        $this->clientFactory = $clientFactory;
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
            $data      = json_decode($request->getContent(), true);
            $message   = new Message($data);
            $validator = new MessageValidator();
            $validator->isValid($message);
        } catch (\Exception $e) {
            return 404;
        }

        if (isset($data['Token']) && isset($data['TopicArn'])) {
            $topicArn = $data['TopicArn'];
            $token    = $data['Token'];

            $topicEntity = $this->entityManager->getRepository('AwsSesMonitorBundle:Topic')->findOneByTopicArn($topicArn);
            if ($topicEntity instanceof Topic) {
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

        return 404;
    }
}
