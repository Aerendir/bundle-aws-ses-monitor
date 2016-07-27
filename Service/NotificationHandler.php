<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Mail;
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
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
            $mail = $this->handleMail($message['mail']);

            if (isset($message['notificationType'])) {
                $return = 500;

                switch ($message['notificationType']) {
                    case self::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS:
                        $return = 200;
                        break;

                    case self::MESSAGE_TYPE_BOUNCE:
                        $return = $this->handleBounceNotification($message, $mail);
                        break;

                    case self::MESSAGE_TYPE_COMPLAINT:
                        $return = $this->handleComplaintNotification($message, $mail);
                        break;

                    case self::MESSAGE_TYPE_DELIVERY:
                        $return = $this->handleDeliveryNotification($message, $mail);
                        break;
                }

                // Flush all entities
                $this->entityManager->flush();

                return $return;
            }
        }

        return 404;
    }

    /**
     * @param array $message
     *
     * @return int
     */
    private function handleBounceNotification(array $message, Mail $mail)
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
    private function handleComplaintNotification(array $message, Mail $mail)
    {
        foreach ($message['complaint']['complainedRecipients'] as $complainedRecipient) {
            $email = $complainedRecipient['emailAddress'];
            $complaint = $this->entityManager->getRepository('AwsSesMonitorBundle:Complaint')->findOneByEmail($email);

            if (null === $complaint) {
                $complaint = new Complaint($email);
            }

            $complaint->setComplaintTime(new \DateTime());

            $this->entityManager->persist($complaint);
        }

        return 200;
    }

    /**
     * @param array $message
     *
     * @return int
     */
    private function handleDeliveryNotification(array $message, Mail $mail)
    {
        foreach ($message['delivery']['recipients'] as $recipient) {
            $delivery = $this->entityManager->getRepository('AwsSesMonitorBundle:Complaint')->findOneByEmail($recipient);

            if (null === $delivery) {
                $delivery = new Delivery($recipient);
            }

            $delivery->setDeliveryTime(new \DateTime());

            $this->entityManager->persist($delivery);
        }

        return 200;
    }

    /**
     * @param $mail
     *
     * @return Mail
     */
    private function handleMail($mail)
    {
        $object = $this->entityManager->getRepository('AwsSesMonitorBundle:Mail')->findOneByMessageId($mail['messageId']);

        // If a Mail object already exists return it
        if (null !== $object)
            return $object;

        $object = new Mail();
        $object->setMessageId($mail['messageId'])
            ->setSentOn(new \DateTime($mail['timestamp']))
            ->setSentFrom($mail['source'])
            ->setSourceArn($mail['sourceArn'])
            ->setSendingAccountId($mail['sendingAccountId']);

        if (isset($mail['headers']))
            $object->setHeaders($mail['headers']);

        if (isset($mail['commonHeaders']))
            $object->setCommonHeaders($mail['commonHeaders']);

        $this->entityManager->persist($object);

        return $object;
    }

    /**
     * @param array $recipient
     * @param array $message
     */
    private function handleBouncedRecipients(array $recipient, array $message)
    {
        $bounce = $this->entityManager->getRepository('AwsSesMonitorBundle:Complaint')->findOneByEmail($recipient['emailAddress']);

        if (null === $bounce) {
            $bounce = new Bounce($recipient['emailAddress']);
        }

        $bounce->incrementBounceCounter()
            ->setLastTimeBounce(new \DateTime())
            ->setType(($message['bounce']['bounceType']))
            ->setSubType(($message['bounce']['bounceSubType']));

        $this->entityManager->persist($bounce);
    }
}
