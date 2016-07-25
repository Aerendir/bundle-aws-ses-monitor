<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MonitorHandlerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\NoopHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\NotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\SubscriptionConfirmationHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates the handlers.
 */
class HandlerFactory
{
    /** @var EntityManager */
    private $_em;

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
        $this->_em = $entityManager;
        $this->awsFactory = $awsFactory;
    }

    /**
     * @param Request $request
     *
     * @return MonitorHandlerInterface
     */
    public function buildBouncesHandler(Request $request)
    {
        return $this->buildHandler($request);
    }

    /**
     * @param Request $request
     *
     * @return MonitorHandlerInterface
     */
    public function buildComplaintsHandler(Request $request)
    {
        return $this->buildHandler($request);
    }

    /**
     * @param Request $request
     *
     * @return MonitorHandlerInterface
     */
    public function buildHandler(Request $request)
    {
        $headerType = $request->headers->get('x-amz-sns-message-type');

        switch ($headerType) {
            case NotificationHandler::HEADER_TYPE:
                return new NotificationHandler(
                    $this->_em->getRepository('AwsSesMonitorBundle:Bounce')
                );

            case SubscriptionConfirmationHandler::HEADER_TYPE:
                return new SubscriptionConfirmationHandler(
                    $this->_em->getRepository('AwsSesMonitorBundle:Topic'),
                    $this->awsFactory
                );

            default:
                return new NoopHandler(); // ignore all other types of messages for now
        }
    }
}
