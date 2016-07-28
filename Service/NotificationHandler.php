<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;
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

            // Create and Persist the MailMessage object
            $mail = $this->handleMailMessage($message['mail']);

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
     * @param MailMessage  $mail
     *
     * @return int
     */
    private function handleBounceNotification(array $message, MailMessage $mail)
    {
        foreach ($message['bounce']['bouncedRecipients'] as $bouncedRecipient) {
            $bounce = new Bounce($bouncedRecipient['emailAddress']);

            $bounce->setMailMessage($mail)
                ->setSentOn(new \DateTime())
                ->setType(($message['bounce']['bounceType']))
                ->setSubType(($message['bounce']['bounceSubType']))
                ->setFeedbackId($message['bounce']['feedbackId']);

            if (isset($message['bounce']['reportingMta']))
                $bounce->setReportingMta($message['bounce']['reportingMta']);

            if (isset($bouncedRecipient['action']))
                $bounce->setAction($bouncedRecipient['action']);

            if (isset($bouncedRecipient['status']))
                $bounce->setAction($bouncedRecipient['status']);

            if (isset($bouncedRecipient['diagnosticCode']))
                $bounce->setAction($bouncedRecipient['diagnosticCode']);

            $this->entityManager->persist($bounce);
        }

        return 200;
    }

    /**
     * @param array $message
     * @param MailMessage  $mail
     *
     * @return int
     */
    private function handleComplaintNotification(array $message, MailMessage $mail)
    {
        foreach ($message['complaint']['complainedRecipients'] as $complainedRecipient) {
            $complaint = new Complaint($complainedRecipient['emailAddress']);

            $complaint->setMailMessage($mail)
                ->setComplaintTime(new \DateTime());

            $this->entityManager->persist($complaint);
        }

        return 200;
    }

    /**
     * @param array $message
     * @param MailMessage  $mail
     *
     * @return int
     */
    private function handleDeliveryNotification(array $message, MailMessage $mail)
    {
        foreach ($message['delivery']['recipients'] as $recipient) {
            $delivery = new Delivery($recipient);

            $delivery->setMailMessage($mail)
                ->setDeliveryTime(new \DateTime());

            $this->entityManager->persist($delivery);
        }

        return 200;
    }

    /**
     * @param $mail
     *
     * @return MailMessage
     */
    private function handleMailMessage($mail)
    {
        $object = $this->entityManager->getRepository('AwsSesMonitorBundle:MailMessage')->findOneByMessageId($mail['messageId']);

        // If a MailMessage object already exists return it
        if (null !== $object)
            return $object;

        $object = new MailMessage();
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
}
