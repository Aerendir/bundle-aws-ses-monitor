<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Mail;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\BounceRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\ComplaintRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\DeliveryRepositoryInterface;
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
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var BounceRepositoryInterface|ComplaintRepositoryInterface|DeliveryRepositoryInterface $repo
     */
    private $repo;

    /**
     * @todo Remove ObjectRepository as it isn't needed anymore
     * @param EntityManager $entityManager
     * @param ObjectRepository $repo
     */
    public function __construct(EntityManager $entityManager, ObjectRepository $repo)
    {
        $this->entityManager = $entityManager;
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

            // Persist the Mail object
            $this->handleMail($message['mail']);

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

            // Flush all entities
            $this->entityManager->flush();
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
            $this->handleBouncedRecipients($bouncedRecipient, $message);
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

    /**
     * @param $mail
     */
    private function handleMail($mail)
    {
        $object = new Mail();
        $object->setMessageId($mail['messageId'])
            ->setSentOn(new \DateTime($mail['timestamp']))
            ->setSentFrom($mail['source'])
            ->setSourceArn($mail['sourceArn'])
            ->setSendingAccountId($mail['sendingAccountId'])
            ->setHeaders($mail['headers'])
            ->setCommonHeaders($mail['commonHeaders']);

        $this->entityManager->persist($object);
    }

    /**
     * @param array $recipient
     * @param array $message
     */
    private function handleBouncedRecipients(array $recipient, array $message)
    {
        $bounce = $this->repo->findOneByEmail($recipient['emailAddress']);

        if (null === $bounce) {
            $bounce = new Bounce($recipient['emailAddress']);
        }

        $bounce->incrementBounceCounter()
            ->setLastTimeBounce(new \DateTime())
            ->setType(($message['bounce']['bounceType'] === 'Permanent'));

        $this->repo->save($bounce);
    }
}
