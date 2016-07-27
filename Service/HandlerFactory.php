<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates the handlers.
 */
class HandlerFactory
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @var AwsClientFactory
     */
    private $awsFactory;

    /**
     * HandlerFactory constructor.
     *
     * @param EntityManager    $entityManager
     * @param AwsClientFactory $awsFactory
     */
    public function __construct(EntityManager $entityManager, AwsClientFactory $awsFactory)
    {
        $this->entityManager = $entityManager;
        $this->awsFactory = $awsFactory;
    }

    /**
     * @param Request $request
     *
     * @return HandlerInterface
     */
    public function buildHandler(Request $request)
    {
        $headerType = $request->headers->get('x-amz-sns-message-type');

        switch ($headerType) {
            case NotificationHandler::HEADER_TYPE:
                return new NotificationHandler($this->entityManager);

            case SubscriptionConfirmationHandler::HEADER_TYPE:
                return new SubscriptionConfirmationHandler($this->entityManager, $this->awsFactory);

            default:
                return new NoopHandler(); // ignore all other types of messages for now
        }
    }
}
