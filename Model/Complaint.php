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
     * Complaint constructor.
     *
     * @param $emailAddress
     * @param $complaintTime
     */
    public function __construct($emailAddress, $complaintTime)
    {
        $this->setEmailAddress($emailAddress);
        $this->complaintTime = $complaintTime;
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
}
