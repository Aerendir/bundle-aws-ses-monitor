<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\BouncerHandlerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\NoopHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\NotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\SubscriptionConfirmationHandler;
use Symfony\Component\HttpFoundation\Request;

class HandlerFactory
{
    /** @var ObjectManager */
    private $objectManager;

    /**
     * @var AwsClientFactory
     */
    private $awsFactory;

    public function __construct(ObjectManager $entityManager, AwsClientFactory $awsFactory)
    {
        $this->objectManager = $entityManager;
        $this->awsFactory = $awsFactory;
    }

    /**
     * @param Request $request
     * @return BouncerHandlerInterface
     */
    public function buildHandler(Request $request)
    {
        $headerType = $request->headers->get('x-amz-sns-message-type');

        switch($headerType) {
            case NotificationHandler::HEADER_TYPE:
                return new NotificationHandler(
                    $this->objectManager->getRepository('AwsSesMonitorBundle:Bounce')
                );

            case SubscriptionConfirmationHandler::HEADER_TYPE:
                return new SubscriptionConfirmationHandler(
                    $this->objectManager->getRepository('AwsSesMonitorBundle:Topic'),
                    $this->awsFactory
                );

            default:
                return new NoopHandler(); // ignore all other types of messages for now
        }
    }
}
