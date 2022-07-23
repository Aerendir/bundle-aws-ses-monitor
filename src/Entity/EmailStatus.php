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
     * @ORM\Column(unique=true)
     * @ORM\Id
     */
    private string $address;

    /** @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce", mappedBy="emailStatus") */
    private Collection $bounces;

    /** @ORM\Column(name="hard_bounces_count", type="integer") */
    private int $hardBouncesCount = 0;

    /** @ORM\Column(name="soft_bounces_count", type="integer") */
    private int $softBouncesCount = 0;

    /** @ORM\Column(name="last_bounce_type", type="string", nullable=true) */
    private ?string $lastBounceType = null;

    /** @ORM\Column(name="last_time_bounced", type="datetime", nullable=true) */
    private ?\DateTimeInterface $lastTimeBounced = null;

    /** @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint", mappedBy="emailStatus") */
    private Collection $complaints;

    /** @ORM\Column(name="last_time_complained", type="datetime", nullable=true) */
    private ?\DateTimeInterface $lastTimeComplained = null;

    /** @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery", mappedBy="emailStatus") */
    private Collection $deliveries;

    /** @ORM\Column(name="last_time_delivered", type="datetime", nullable=true) */
    private ?\DateTimeInterface $lastTimeDelivered = null;

    public function __construct(string $email)
    {
        $this->address    = \mb_strtolower($email);
        $this->bounces    = new ArrayCollection();
        $this->complaints = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getBounces(): Collection
    {
        return $this->bounces;
    }

    public function getHardBouncesCount(): int
    {
        return $this->hardBouncesCount;
    }

    public function getSoftBouncesCount(): int
    {
        return $this->softBouncesCount;
    }

    public function getLastBounceType(): ?string
    {
        return $this->lastBounceType;
    }

    public function getLastTimeBounced(): ?\DateTimeInterface
    {
        return $this->lastTimeBounced;
    }

    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    public function getLastTimeComplained(): ?\DateTimeInterface
    {
        return $this->lastTimeComplained;
    }

    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function getLastTimeDelivered(): ?\DateTimeInterface
    {
        return $this->lastTimeDelivered;
    }

    /**
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
