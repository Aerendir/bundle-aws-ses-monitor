<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an email address and records information about its health.
 *
 * @ORM\Table(name="shq_aws_ses_monitor_email_statuses", indexes={@ORM\Index(name="emails_statuses", columns={"address"})})
 * @ORM\Entity()
 */
class EmailStatus
{
    /**
     * @var string
     * @ORM\Column(unique=true)
     * @ORM\Id
     */
    private $address;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce", mappedBy="emailStatus")
     */
    private $bounces;

    /**
     * @var int
     * @ORM\Column(name="hard_bounces_count", type="integer")
     */
    private $hardBouncesCount = 0;

    /**
     * @var int
     * @ORM\Column(name="soft_bounces_count", type="integer")
     */
    private $softBouncesCount = 0;

    /**
     * @var string|null
     * @ORM\Column(name="last_bounce_type", type="string", nullable=true)
     */
    private $lastBounceType;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_time_bounced", type="datetime", nullable=true)
     */
    private $lastTimeBounced;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint", mappedBy="emailStatus")
     */
    private $complaints;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_time_complained", type="datetime", nullable=true)
     */
    private $lastTimeComplained;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery", mappedBy="emailStatus")
     */
    private $deliveries;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_time_delivered", type="datetime", nullable=true)
     */
    private $lastTimeDelivered;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->address    = \mb_strtolower($email);
        $this->bounces    = new ArrayCollection();
        $this->complaints = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return Collection
     */
    public function getBounces(): Collection
    {
        return $this->bounces;
    }

    /**
     * @return int
     */
    public function getHardBouncesCount(): int
    {
        return $this->hardBouncesCount;
    }

    /**
     * @return int
     */
    public function getSoftBouncesCount(): int
    {
        return $this->softBouncesCount;
    }

    /**
     * @return string|null
     */
    public function getLastBounceType(): ?string
    {
        return $this->lastBounceType;
    }

    public function getLastTimeBounced(): ?\DateTimeInterface
    {
        return $this->lastTimeBounced;
    }

    /**
     * @return Collection
     */
    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    public function getLastTimeComplained(): ?\DateTimeInterface
    {
        return $this->lastTimeComplained;
    }

    /**
     * @return Collection
     */
    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function getLastTimeDelivered(): ?\DateTimeInterface
    {
        return $this->lastTimeDelivered;
    }

    /**
     * @param Bounce $bounce
     *
     * @internal
     */
    public function addBounce(Bounce $bounce): self
    {
        // Add only if not already added to avoid circular references
        $predictate = function (/** @noinspection PhpUnusedParameterInspection */ $key, Bounce $element) use ($bounce): bool {
            return $element->getFeedbackId() === $bounce->getFeedbackId();
        };

        if (false === $this->bounces->exists($predictate)) {
            $this->bounces->add($bounce);

            $this->lastBounceType  = $bounce->getType();
            $this->lastTimeBounced = $bounce->getBouncedOn();

            if (Bounce::TYPE_PERMANENT === $this->getLastBounceType()) {
                ++$this->hardBouncesCount;
            }

            if (Bounce::TYPE_TRANSIENT === $this->getLastBounceType()) {
                ++$this->softBouncesCount;
            }
        }

        return $this;
    }

    /**
     * @param Complaint $complaint
     *
     * @internal
     */
    public function addComplaint(Complaint $complaint): self
    {
        // Add only if not already added to avoid circular references
        $predictate = function (/** @noinspection PhpUnusedParameterInspection */ $key, Complaint $element) use ($complaint): bool {
            return $element->getFeedbackId() === $complaint->getFeedbackId();
        };

        if (false === $this->complaints->exists($predictate)) {
            $this->complaints->add($complaint);

            $this->lastTimeComplained = $complaint->getComplainedOn();
        }

        return $this;
    }

    /**
     * @param Delivery $delivery
     *
     * @internal
     */
    public function addDelivery(Delivery $delivery): self
    {
        // Add only if not already added to avoid circular references
        $predictate = function (/** @noinspection PhpUnusedParameterInspection */ $key, Delivery $element) use ($delivery): bool {
            // A Delivery doesn't have a feedbackId, so we rely on timestamp that should be sufficient to get identity uniqueness
            return $element->getDeliveredOn()->getTimestamp() === $delivery->getDeliveredOn()->getTimestamp();
        };

        if (false === $this->deliveries->exists($predictate)) {
            $this->deliveries->add($delivery);

            $this->lastTimeDelivered = $delivery->getDeliveredOn();
        }

        return $this;
    }
}
