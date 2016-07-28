<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A EmailStatus of an email address.
 */
class EmailStatus
{
    /** @var  string $emailAddress */
    private $emailAddress;

    /**
     * @var ArrayCollection $bounces
     */
    private $bounces;

    /** @var  int $hardBouncesCount */
    private $hardBouncesCount = 0;

    /** @var  int $hardBouncesCount */
    private $softBouncesCount = 0;

    /**
     * @var string $lastBounceType
     */
    private $lastBounceType;

    /**
     * @var \DateTime $lastBounceTime
     */
    private $lastBounceTime;

    /**
     * @var ArrayCollection $complaints
     */
    private $complaints;

    /** @var  int $complaintsCount */
    private $complaintsCount = 0;

    /**
     * @var \DateTime $lastComplaintTime
     */
    private $lastComplaintTime;

    /**
     * @var ArrayCollection $deliveries
     */
    private $deliveries;

    /** @var  int $deliveriesCount */
    private $deliveriesCount = 0;

    /**
     * @var \DateTime $lastDeliveryTime
     */
    private $lastDeliveryTime;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->emailAddress = mb_strtolower($email);;
        $this->bounces    = new ArrayCollection();
        $this->complaints = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
    }

    /**
     * @param Bounce $bounce
     *
     * @return $this
     */
    public function addBounce(Bounce $bounce)
    {
        $bounce->setEmailAddress($this->getEmailAddress());
        $this->lastBounceType = $bounce->getType();
        $this->lastBounceTime = $bounce->getBouncedOn();

        if ($this->getLastBounceType() === Bounce::TYPE_PERMANENT)
            $this->hardBouncesCount++;

        if ($this->getLastBounceType() === Bounce::TYPE_TRANSIENT)
            $this->softBouncesCount++;

        return $this;
    }

    /**
     * @param Complaint $complaint
     *
     * @return $this
     */
    public function addComplaint(Complaint $complaint)
    {
        $complaint->setEmailAddress($this->getEmailAddress());
        $this->complaintsCount++;
        $this->lastComplaintTime = $complaint->getComplainedOn();

        return $this;
    }

    /**
     * @param Delivery $delivery
     *
     * @return $this
     */
    public function addDelivery(Delivery $delivery)
    {
        $delivery->setEmailAddress($this->getEmailAddress());
        $this->deliveriesCount++;
        $this->lastDeliveryTime = $delivery->getDeliveryTime();

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return ArrayCollection
     */
    public function getBounces()
    {
        return $this->bounces;
    }

    /**
     * @return int
     */
    public function getHardBouncesCount()
    {
        return $this->hardBouncesCount;
    }

    /**
     * @return int
     */
    public function getSoftBouncesCount()
    {
        return $this->softBouncesCount;
    }

    /**
     * @return string
     */
    public function getLastBounceType()
    {
        return $this->lastBounceType;
    }

    /**
     * @return \DateTime
     */
    public function getLastBounceTime()
    {
        return $this->lastBounceTime;
    }

    /**
     * @return ArrayCollection
     */
    public function getComplaints()
    {
        return $this->complaints;
    }

    /**
     * @return int
     */
    public function getComplaintsCount()
    {
        return $this->complaintsCount;
    }

    /**
     * @return \DateTime
     */
    public function getLastComplaintTime()
    {
        return $this->lastComplaintTime;
    }

    /**
     * @return ArrayCollection
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * @return int
     */
    public function getDeliveriesCount()
    {
        return $this->deliveriesCount;
    }

    /**
     * @return \DateTime
     */
    public function getLastDeliveryTime()
    {
        return $this->lastDeliveryTime;
    }
}
