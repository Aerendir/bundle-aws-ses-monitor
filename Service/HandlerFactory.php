<?php
namespace Shivas\BouncerBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Shivas\BouncerBundle\Model\BouncerHandlerInterface;
use Shivas\BouncerBundle\Model\NoopHandler;
use Shivas\BouncerBundle\Model\NotificationHandler;
use Shivas\BouncerBundle\Model\SubscriptionConfirmationHandler;
use Symfony\Component\HttpFoundation\Request;

class HandlerFactory
{
    /** @var ObjectManager */
    private $objectManager;

    /**
     * @var AwsClientFactory
     */
    private $awsFactory;

    function __construct(ObjectManager $entityManager, AwsClientFactory $awsFactory)
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
                    $this->objectManager->getRepository('ShivasBouncerBundle:Bounce')
                );

            case SubscriptionConfirmationHandler::HEADER_TYPE:
                return new SubscriptionConfirmationHandler(
                    $this->objectManager->getRepository('ShivasBouncerBundle:Topic'),
                    $this->awsFactory
                );

            default:
                return new NoopHandler(); // ignore all other types of messages for now
        }
    }
}
