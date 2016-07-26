<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\BounceRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles notifications.
 */
class NotificationHandler implements HandlerInterface
{
    const HEADER_TYPE = 'Notification';
    const MESSAGE_TYPE_SUBSCRIPTION_SUCCESS = 'AmazonSnsSubscriptionSucceeded';
    const MESSAGE_TYPE_BOUNCE = 'Bounce';
    const MESSAGE_TYPE_COMPLAINT = 'Complaint';
    const MESSAGE_TYPE_DELIVERY = 'Delivery';

    /**
     * @var BounceRepositoryInterface
     */
    private $repo;

    /**
     * @param ObjectRepository $repo
     */
    public function __construct(ObjectRepository $repo)
    {
        $this->repo = $repo;
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
            $data = json_decode($request->getContent(), true);
            $message = new Message($data);
            $validator = new MessageValidator();
            $validator->validate($message);
        } catch (\Exception $e) {
            return 403; // not valid message, we return 404
        }

        if (isset($data['Message'])) {
            $message = json_decode($data['Message'], true);
            if (isset($message['notificationType'])) {
                switch ($message['notificationType']) {
                    case self::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS:
                        return 200;
                        break;

                    case self::MESSAGE_TYPE_BOUNCE:
                        return $this->handleBounceNotification($message);
                        break;

                    case self::MESSAGE_TYPE_COMPLAINT:
                        return $this->handleComplaintNotification($message);
                        break;

                    case self::MESSAGE_TYPE_DELIVERY:
                        return $this->handleDeliveryNotification($message);
                        break;
                }
            }
        }

        return 404;
    }

    /**
     * @param array $message
     *
     * @return int
     */
    private function handleBounceNotification(array $message)
    {
        foreach ($message['bounce']['bouncedRecipients'] as $bouncedRecipient) {
            $email = $bouncedRecipient['emailAddress'];
            $bounce = $this->repo->findOneByEmail($email);

            if (null === $bounce) {
                $bounce = new Bounce($email);
            }

            $bounce->incrementBounceCounter()
                ->setLastTimeBounce(new \DateTime())
                ->setPermanent(($message['bounce']['bounceType'] === 'Permanent'));

            $this->repo->save($bounce);
        }

        return 200;
    }

    /**
     * @param array $message
     *
     * @return int
     */
    private function handleComplaintNotification(array $message)
    {
        foreach ($message['complaint']['complainedRecipients'] as $complainedRecipient) {
            $email = $complainedRecipient['emailAddress'];
            $complaint = $this->repo->findOneByEmail($email);

            if (null === $complaint) {
                $complaint = new Complaint($email);
            }

            $complaint->setComplaintTime(new \DateTime());

            $this->repo->save($complaint);
        }

        return 200;
    }

    /**
     * @param array $message
     *
     * @return int
     */
    private function handleDeliveryNotification(array $message)
    {
        foreach ($message['delivery']['recipients'] as $recipient) {
            $delivery = $this->repo->findOneByEmail($recipient);

            if (null === $delivery) {
                $delivery = new Delivery($recipient);
            }

            $delivery->setDeliveryTime(new \DateTime());

            $this->repo->save($delivery);
        }

        return 200;
    }
}
