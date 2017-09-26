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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * A EmailStatus of an email address.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class EmailStatus
{
    /** @var string $emailAddress */
    private $emailAddress;

    /** @var ArrayCollection */
    private $bounces;

    /** @var int $hardBouncesCount */
    private $hardBouncesCount = 0;

    /** @var int $hardBouncesCount */
    private $softBouncesCount = 0;

    /** @var string */
    private $lastBounceType;

    /** @var \DateTime */
    private $lastTimeBounced;

    /** @var ArrayCollection */
    private $complaints;

    /** @var int $complaintsCount */
    private $complaintsCount = 0;

    /** @var \DateTime */
    private $lastTimeComplained;

    /** @var ArrayCollection */
    private $deliveries;

    /** @var int $deliveriesCount */
    private $deliveriesCount = 0;

    /** @var \DateTime */
    private $lastTimeDelivered;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->emailAddress = mb_strtolower($email);
        $this->bounces      = new ArrayCollection();
        $this->complaints   = new ArrayCollection();
        $this->deliveries   = new ArrayCollection();
    }

    /**
     * @param Bounce $bounce
     *
     * @return $this
     */
    public function addBounce(Bounce $bounce)
    {
        $bounce->setEmailAddress($this->getEmailAddress());
        $this->lastBounceType  = $bounce->getType();
        $this->lastTimeBounced = $bounce->getBouncedOn();

        if (Bounce::TYPE_PERMANENT === $this->getLastBounceType()) {
            ++$this->hardBouncesCount;
        }

        if (Bounce::TYPE_TRANSIENT === $this->getLastBounceType()) {
            ++$this->softBouncesCount;
        }

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
        ++$this->complaintsCount;
        $this->lastTimeComplained = $complaint->getComplainedOn();

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
        ++$this->deliveriesCount;
        $this->lastTimeDelivered = $delivery->getDeliveredOn();

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
    public function getLastTimeBounced()
    {
        return $this->lastTimeBounced;
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
    public function getLastTimeComplained()
    {
        return $this->lastTimeComplained;
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
    public function getLastTimeDelivered()
    {
        return $this->lastTimeDelivered;
    }
}
