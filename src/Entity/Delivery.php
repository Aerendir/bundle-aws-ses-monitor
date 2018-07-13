<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a Delivery.
 *
 * @see https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#delivery-object
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * @ORM\Table(name="shq_aws_ses_monitor_deliveries")
 * @ORM\Entity()
 */
class Delivery
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Email
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email", inversedBy="deliveries", cascade={"persist"})
     * @ORM\JoinColumn(name="email", referencedColumnName="address")
     */
    private $email;

    /**
     * The MessageObject that reported this complaint.
     *
     * @var MailMessage
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage", inversedBy="deliveries")
     */
    private $mailMessage;

    /**
     * The time Amazon SES delivered the email to the recipient's mail server (in ISO8601 format).
     *
     * @var \DateTime
     * @ORM\Column(name="delivered_on", type="datetime")
     */
    private $deliveredOn;

    /**
     * The time in milliseconds between when Amazon SES accepted the request from the sender to passing the message to
     * the recipient's mail server.
     *
     * @var int
     * @ORM\Column(name="processing_time_millis", type="integer")
     */
    private $processingTimeMillis;

    /**
     * The SMTP response message of the remote ISP that accepted the email from Amazon SES.
     *
     * This message will vary by email, by receiving mail server, and by receiving ISP.
     *
     * @var string
     * @ORM\Column(name="smtp_response", type="text")
     */
    private $smtpResponse;

    /**
     * The host name of the Amazon SES mail server that sent the mail.
     *
     * @var string|null
     * @ORM\Column(name="reporting_mta", type="string", nullable=true)
     */
    private $reportingMta;

    /**
     * @param Email       $email
     * @param MailMessage $mailMessage
     */
    public function __construct(Email $email, MailMessage $mailMessage)
    {
        $this->email = $email;
        $this->setMailMessage($mailMessage);

        $this->email->addDelivery($this);
    }

    /**
     * @param Email       $email
     * @param MailMessage $mailMessage
     * @param array       $notification
     *
     * @return Delivery
     */
    public static function create(Email $email, MailMessage $mailMessage, array $notification): Delivery
    {
        $delivery = (new self($email, $mailMessage))
            ->setDeliveredOn(new \DateTime($notification['delivery']['timestamp']))
            ->setProcessingTimeMillis($notification['delivery']['processingTimeMillis'])
            ->setSmtpResponse($notification['delivery']['smtpResponse']);

        if (isset($notification['delivery']['reportingMta'])) {
            $delivery->setReportingMta($notification['delivery']['reportingMta']);
        }

        return $delivery;
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return MailMessage
     */
    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveredOn(): \DateTime
    {
        return $this->deliveredOn;
    }

    /**
     * @return int
     */
    public function getProcessingTimeMillis(): int
    {
        return $this->processingTimeMillis;
    }

    /**
     * @return string
     */
    public function getSmtpResponse(): string
    {
        return $this->smtpResponse;
    }

    /**
     * @return string|null
     */
    public function getReportingMta(): ?string
    {
        return $this->reportingMta;
    }

    /**
     * @param MailMessage $mailMessage
     *
     * @return Delivery
     */
    private function setMailMessage(MailMessage $mailMessage): Delivery
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addDelivery($this);

        return $this;
    }

    /**
     * @param \DateTime $deliveredOn
     *
     * @return Delivery
     */
    private function setDeliveredOn(\DateTime $deliveredOn): Delivery
    {
        $this->deliveredOn = $deliveredOn;

        return $this;
    }

    /**
     * @param int $processingTimeMillis
     *
     * @return Delivery
     */
    private function setProcessingTimeMillis(int $processingTimeMillis): Delivery
    {
        $this->processingTimeMillis = $processingTimeMillis;

        return $this;
    }

    /**
     * @param string $smtpResponse
     *
     * @return Delivery
     */
    private function setSmtpResponse(string $smtpResponse): Delivery
    {
        $this->smtpResponse = $smtpResponse;

        return $this;
    }

    /**
     * @param string $reportingMta
     *
     * @return Delivery
     */
    private function setReportingMta(string $reportingMta): Delivery
    {
        $this->reportingMta = $reportingMta;

        return $this;
    }
}