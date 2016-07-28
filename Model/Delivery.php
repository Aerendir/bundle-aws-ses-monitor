<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Represents a Delivery.
 */
class Delivery
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * The MessageObject that reported this complaint.
     *
     * @var MailMessage $mailMessage
     */
    private $mailMessage;

    /**
     * @var string $emailAddress
     */
    private $emailAddress;

    /**
     * The time Amazon SES delivered the email to the recipient's mail server (in ISO8601 format).
     *
     * @var \DateTime
     */
    private $deliveryTime;

    /**
     * The time in milliseconds between when Amazon SES accepted the request from the sender to passing the message to
     * the recipient's mail server.
     *
     * @var string $processingTimeMillis
     */
    private $processingTimeMillis;

    /**
     * The SMTP response message of the remote ISP that accepted the email from Amazon SES.
     *
     * This message will vary by email, by receiving mail server, and by receiving ISP.
     *
     * @var string $smtpResponse
     */
    private $smtpResponse;

    /**
     * The host name of the Amazon SES mail server that sent the mail.
     *
     * @var string $reportingMta
     */
    private $reportingMta;

    /**
     * @var EmailStatus $emailStatus
     */
    private $emailStatus;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MailMessage
     */
    public function getMailMessage()
    {
        return $this->mailMessage;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @return string
     */
    public function getProcessingTimeMillis()
    {
        return $this->processingTimeMillis;
    }

    /**
     * @return string
     */
    public function getSmtpResponse()
    {
        return $this->smtpResponse;
    }

    /**
     * @return string
     */
    public function getReportingMta()
    {
        return $this->reportingMta;
    }

    /**
     * @return EmailStatus
     */
    public function getEmailStatus()
    {
        return $this->emailStatus;
    }

    /**
     * @param string $email
     */
    public function setEmailAddress($email)
    {
        $this->emailAddress = $email;
    }

    /**
     * @param MailMessage $mailMessage
     *
     * @return $this
     */
    public function setMailMessage(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;

        return $this;
    }

    /**
     * @param \DateTime $deliveryTime
     *
     * @return $this
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * @param string $processingTimeMillis
     *
     * @return $this
     */
    public function setProcessingTimeMillis($processingTimeMillis)
    {
        $this->processingTimeMillis = $processingTimeMillis;

        return $this;
    }

    /**
     * @param string $smtpResponse
     *
     * @return $this
     */
    public function setSmtpResponse($smtpResponse)
    {
        $this->smtpResponse = $smtpResponse;

        return $this;
    }

    /**
     * @param string $reportingMta
     *
     * @return $this
     */
    public function setReportingMta($reportingMta)
    {
        $this->reportingMta = $reportingMta;

        return $this;
    }
}
