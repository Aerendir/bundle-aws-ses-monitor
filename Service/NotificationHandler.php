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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\EmailStatus;
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
     * @var EntityManager
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
            $mailMessage = $this->handleMailMessage($message['mail']);

            if (isset($message['notificationType'])) {
                $return = 500;

                switch ($message['notificationType']) {
                    case self::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS:
                        $return = 200;
                        break;

                    case self::MESSAGE_TYPE_BOUNCE:
                        $return = $this->handleBounceNotification($message, $mailMessage);
                        break;

                    case self::MESSAGE_TYPE_COMPLAINT:
                        $return = $this->handleComplaintNotification($message, $mailMessage);
                        break;

                    case self::MESSAGE_TYPE_DELIVERY:
                        $return = $this->handleDeliveryNotification($message, $mailMessage);
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
     * @param array       $message
     * @param MailMessage $mailMessage
     *
     * @return int
     */
    private function handleBounceNotification(array $message, MailMessage $mailMessage)
    {
        foreach ($message['bounce']['bouncedRecipients'] as $bouncedRecipient) {
            $status = $this->getEmailStatus($bouncedRecipient['emailAddress']);

            $bounce = new Bounce();
            $bounce->setMailMessage($mailMessage)
                ->setBouncedOn(new \DateTime($message['bounce']['timestamp']))
                ->setType(($message['bounce']['bounceType']))
                ->setSubType(($message['bounce']['bounceSubType']))
                ->setFeedbackId($message['bounce']['feedbackId']);

            if (isset($message['bounce']['reportingMta'])) {
                $bounce->setReportingMta($message['bounce']['reportingMta']);
            }

            if (isset($bouncedRecipient['action'])) {
                $bounce->setAction($bouncedRecipient['action']);
            }

            if (isset($bouncedRecipient['status'])) {
                $bounce->setAction($bouncedRecipient['status']);
            }

            if (isset($bouncedRecipient['diagnosticCode'])) {
                $bounce->setAction($bouncedRecipient['diagnosticCode']);
            }

            $this->entityManager->persist($bounce);

            $status->addBounce($bounce);
        }

        return 200;
    }

    /**
     * @param array       $message
     * @param MailMessage $mailMessage
     *
     * @return int
     */
    private function handleComplaintNotification(array $message, MailMessage $mailMessage)
    {
        foreach ($message['complaint']['complainedRecipients'] as $complainedRecipient) {
            $status = $this->getEmailStatus($complainedRecipient['emailAddress']);

            $complaint = new Complaint();
            $complaint->setMailMessage($mailMessage)
                ->setComplainedOn(new \DateTime($message['complaint']['timestamp']))
                ->setFeedbackId($message['complaint']['feedbackId']);

            if (isset($message['complaint']['userAgent'])) {
                $complaint->setUserAgent($message['complaint']['userAgent']);
            }

            if (isset($message['complaint']['complaintFeedbackType'])) {
                $complaint->setComplaintFeedbackType($message['complaint']['complaintFeedbackType']);
            }

            if (isset($message['complaint']['arrivalDate'])) {
                $complaint->setArrivalDate($message['complaint']['arrivalDate']);
            }

            $status->addComplaint($complaint);
            $this->entityManager->persist($complaint);
        }

        return 200;
    }

    /**
     * @param array       $message
     * @param MailMessage $mailMessage
     *
     * @return int
     */
    private function handleDeliveryNotification(array $message, MailMessage $mailMessage)
    {
        foreach ($message['delivery']['recipients'] as $recipient) {
            $status = $this->getEmailStatus($recipient);

            $delivery = new Delivery();
            $delivery->setMailMessage($mailMessage)
                ->setDeliveryTime(new \DateTime($message['delivery']['timestamp']))
                ->setProcessingTimeMillis($message['delivery']['processingTimeMillis'])
                ->setSmtpResponse($message['delivery']['smtpResponse']);

            if (isset($message['delivery']['reportingMta'])) {
                $delivery->setReportingMta($message['delivery']['reportingMta']);
            }

            $status->addDelivery($delivery);
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
        if (null !== $object) {
            return $object;
        }

        $object = new MailMessage();
        $object->setMessageId($mail['messageId'])
            ->setSentOn(new \DateTime($mail['timestamp']))
            ->setSentFrom($mail['source'])
            ->setSourceArn($mail['sourceArn'])
            ->setSendingAccountId($mail['sendingAccountId']);

        if (isset($mail['headers'])) {
            $object->setHeaders($mail['headers']);
        }

        if (isset($mail['commonHeaders'])) {
            $object->setCommonHeaders($mail['commonHeaders']);
        }

        $this->entityManager->persist($object);

        return $object;
    }

    /**
     * @param string $email
     *
     * @return EmailStatus
     */
    private function getEmailStatus($email)
    {
        $status = $this->entityManager->getRepository('AwsSesMonitorBundle:EmailStatus')->findOneByEmailAddress($email);

        if (null === $status) {
            $status = new EmailStatus($email);
            $this->entityManager->persist($status);

            return $status;
        }

        return $status;
    }
}
