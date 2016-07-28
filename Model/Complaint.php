<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Represents a Complaint.
 */
class Complaint
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var
     */
    private $mailMessage;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var \DateTime
     */
    private $complaintTime;

    /**
     * @param $email
     */
    public function __construct($email)
    {
        $this->emailAddress = mb_strtolower($email);
    }

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
    public function getComplaintTime()
    {
        return $this->complaintTime;
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
     * @param \DateTime $complaintTime
     *
     * @return $this
     */
    public function setComplaintTime($complaintTime)
    {
        $this->complaintTime = $complaintTime;

        return $this;
    }
}
