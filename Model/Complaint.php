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
    private $mail;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $complaintTime;

    /**
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = mb_strtolower($email);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \DateTime
     */
    public function getComplaintTime()
    {
        return $this->complaintTime;
    }

    /**
     * @param Mail $mail
     *
     * @return $this
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;

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
