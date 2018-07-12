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
 * Represents a Complaint.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#complaint-object
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * @ORM\Table(name="shq_aws_ses_monitor_complaints")
 * @ORM\Entity()
 */
class Complaint
{
    /** Indicates unsolicited email or some other kind of email abuse. */
    const TYPE_ABUSE = 'abuse';

    /** Email authentication failure report. */
    const TYPE_AUTH_FAILURE = 'auth-failure';

    /** Indicates some kind of fraud or phishing activity. */
    const TYPE_FRAUD = 'fraud';

    /**
     * Indicates that the entity providing the report does not consider the message to be spam.
     * This may be used to correct a message that was incorrectly tagged or categorized as spam.
     */
    const TYPE_NOT_SPAM = 'not-spam';

    /** Indicates any other feedback that does not fit into other registered types. */
    const TYPE_OTHER = 'other';

    /** Reports that a virus is found in the originating message. */
    const TYPE_VIRUS = 'virus';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var Email
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email", inversedBy="complaints")
     * @ORM\JoinColumn(name="email", referencedColumnName="address")
     */
    private $email;

    /**
     * The MessageObject that reported this complaint.
     *
     * @var MailMessage
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage", inversedBy="complaints")
     *
     * @todo Review the relation
     */
    private $mailMessage;

    /**
     * The date and time at which the bounce was sent (in ISO8601 format).
     *
     * Note that this is the time at which the notification was sent by the ISP, and not the time at which it was
     * received by Amazon SES.
     *
     * @var \DateTime
     * @ORM\Column(name="complained_on", type="datetime")
     */
    private $complainedOn;

    /**
     * A unique ID for the bounce.
     *
     * @var string
     * @ORM\Column(name="feedback_id", type="string")
     */
    private $feedbackId;

    /**
     * The value of the User-Agent field from the feedback report.
     *
     * This indicates the name and version of the system that generated the report.
     *
     * @var string
     * @ORM\Column(name="user_agent")
     */
    private $userAgent;

    /**
     * The value of the Feedback-Type field from the feedback report received from the ISP.
     *
     * This contains the type of feedback.
     *
     * @var string|null
     * @ORM\Column(name="complaint_feedback_type", type="string", nullable=true)
     */
    private $complaintFeedbackType;

    /**
     * The value of the Arrival-Date or Received-Date field from the feedback report (in ISO8601 format).
     *
     * This field may be absent in the report (and therefore also absent in the JSON).
     *
     * @var \DateTime|null
     * @ORM\Column(name="arrival_date", type="datetime", nullable=true)
     */
    private $arrivalDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return MailMessage
     */
    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return \DateTime
     */
    public function getComplainedOn(): \DateTime
    {
        return $this->complainedOn;
    }

    /**
     * @return string
     */
    public function getFeedbackId(): string
    {
        return $this->feedbackId;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return string|null
     */
    public function getComplaintFeedbackType(): ? string
    {
        return $this->complaintFeedbackType;
    }

    /**
     * @return \DateTime|null
     */
    public function getArrivalDate(): ? \DateTime
    {
        return $this->arrivalDate;
    }

    /**
     * @param Email $email
     *
     * @return Complaint
     *
     * @internal
     */
    public function setEmail(Email $email): Complaint
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param MailMessage $mailMessage
     *
     * @internal
     *
     * @return Complaint
     */
    public function setMailMessage(MailMessage $mailMessage): Complaint
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addComplaint($this);

        return $this;
    }

    /**
     * @param \DateTime $complainedOn
     *
     * @internal
     *
     * @return Complaint
     */
    public function setComplainedOn(\DateTime $complainedOn): Complaint
    {
        $this->complainedOn = $complainedOn;

        return $this;
    }

    /**
     * @param string $feedbackId
     *
     * @internal
     *
     * @return Complaint
     */
    public function setFeedbackId(string $feedbackId): Complaint
    {
        $this->feedbackId = $feedbackId;

        return $this;
    }

    /**
     * @param string $userAgent
     *
     * @internal
     *
     * @return Complaint
     */
    public function setUserAgent(string $userAgent): Complaint
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @param string $complaintFeedbackType
     *
     * @internal
     *
     * @return Complaint
     */
    public function setComplaintFeedbackType(string $complaintFeedbackType): Complaint
    {
        $this->complaintFeedbackType = $complaintFeedbackType;

        return $this;
    }

    /**
     * @param \DateTime $arrivalDate
     *
     * @internal
     *
     * @return Complaint
     */
    public function setArrivalDate(\DateTime $arrivalDate): Complaint
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }
}
