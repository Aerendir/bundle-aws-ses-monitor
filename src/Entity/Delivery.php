<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a Delivery.
 *
 * @see https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#delivery-object
 *
 * @ORM\Table(name="shq_aws_ses_monitor_deliveries")
 * @ORM\Entity()
 */
class Delivery
{
    /** @var string */
    private const DELIVERY = 'delivery';

    /**
     * @var int
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EmailStatus", inversedBy="deliveries", cascade={"persist"})
     * @ORM\JoinColumn(name="email_status", referencedColumnName="address")
     */
    private EmailStatus $emailStatus;

    /**
     * The MessageObject that reported this complaint.
     *
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage", inversedBy="deliveries")
     * @ORM\JoinColumn(name="mail_message", referencedColumnName="message_id")
     */
    private MailMessage $mailMessage;

    /**
     * The time Amazon SES delivered the email to the recipient's mail server (in ISO8601 format).
     *
     * @ORM\Column(name="delivered_on", type="datetime")
     */
    private \DateTimeInterface $deliveredOn;

    /**
     * The time in milliseconds between when Amazon SES accepted the request from the sender to passing the message to
     * the recipient's mail server.
     *
     * @ORM\Column(name="processing_time_millis", type="integer")
     */
    private int $processingTimeMillis;

    /**
     * The SMTP response message of the remote ISP that accepted the email from Amazon SES.
     *
     * This message will vary by email, by receiving mail server, and by receiving ISP.
     *
     * @ORM\Column(name="smtp_response", type="text")
     */
    private string $smtpResponse;

    /**
     * The host name of the Amazon SES mail server that sent the mail.
     *
     * @ORM\Column(name="reporting_mta", type="string", nullable=true)
     */
    private ?string $reportingMta = null;

    public static function create(EmailStatus $email, MailMessage $mailMessage, array $notification): Delivery
    {
        $delivery = (new self())
            ->setDeliveredOn(new \DateTime($notification[self::DELIVERY]['timestamp']))
            ->setProcessingTimeMillis($notification[self::DELIVERY]['processingTimeMillis'])
            ->setSmtpResponse($notification[self::DELIVERY]['smtpResponse'])
            ->setMailMessage($mailMessage)
            ->setEmailStatus($email);

        if (isset($notification[self::DELIVERY]['reportingMta'])) {
            $delivery->setReportingMta($notification[self::DELIVERY]['reportingMta']);
        }

        return $delivery;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getEmailStatus(): EmailStatus
    {
        return $this->emailStatus;
    }

    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    public function getDeliveredOn(): \DateTimeInterface
    {
        return $this->deliveredOn;
    }

    public function getProcessingTimeMillis(): int
    {
        return $this->processingTimeMillis;
    }

    public function getSmtpResponse(): string
    {
        return $this->smtpResponse;
    }

    public function getReportingMta(): ?string
    {
        return $this->reportingMta;
    }

    private function setEmailStatus(EmailStatus $emailStatus): self
    {
        $this->emailStatus = $emailStatus;
        $emailStatus->addDelivery($this);

        return $this;
    }

    private function setMailMessage(MailMessage $mailMessage): self
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addDelivery($this);

        return $this;
    }

    private function setDeliveredOn(\DateTimeInterface $deliveredOn): self
    {
        $this->deliveredOn = $deliveredOn;

        return $this;
    }

    private function setProcessingTimeMillis(int $processingTimeMillis): self
    {
        $this->processingTimeMillis = $processingTimeMillis;

        return $this;
    }

    private function setSmtpResponse(string $smtpResponse): self
    {
        $this->smtpResponse = $smtpResponse;

        return $this;
    }

    private function setReportingMta(string $reportingMta): self
    {
        $this->reportingMta = $reportingMta;

        return $this;
    }
}
