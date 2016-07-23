<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Represents a Complaint.
 */
class Complaint
{
    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var \DateTime
     */
    protected $complaintTime;

    /**
     * @var bool
     */
    protected $permanent;

    /**
     * Complaint constructor.
     *
     * @param $emailAddress
     * @param $complaintTime
     * @param bool $permanent
     */
    public function __construct($emailAddress, $complaintTime, $permanent = false)
    {
        $this->setEmailAddress($emailAddress);
        $this->complaintTime = $complaintTime;
        $this->permanent = $permanent;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     *
     * @return $this
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = mb_strtolower($emailAddress);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getComplaintTime()
    {
        return $this->complaintTime;
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

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param bool $permanent
     *
     * @return $this
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }
}
