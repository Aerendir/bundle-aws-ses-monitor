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

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a Complaint.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#complaint-object
 *
 * @ORM\Table(name="shq_aws_ses_monitor_complaints")
 * @ORM\Entity()
 */
class Complaint
{
    /** Indicates unsolicited email or some other kind of email abuse.
     * @var string */
    public const TYPE_ABUSE = 'abuse';

    /** Email authentication failure report.
     * @var string */
    public const TYPE_AUTH_FAILURE = 'auth-failure';

    /** Indicates some kind of fraud or phishing activity.
     * @var string */
    public const TYPE_FRAUD = 'fraud';

    /**
     * Indicates that the entity providing the report does not consider the message to be spam.
     * This may be used to correct a message that was incorrectly tagged or categorized as spam.
     *
     * @var string
     */
    public const TYPE_NOT_SPAM = 'not-spam';

    /** Indicates any other feedback that does not fit into other registered types.
     * @var string */
    public const TYPE_OTHER = 'other';

    /** Reports that a virus is found in the originating message.
     * @var string */
    public const TYPE_VIRUS = 'virus';

    /** @var string */
    private const COMPLAINT = 'complaint';

    /**
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="EmailStatus", inversedBy="complaints", cascade={"persist"})
     * @ORM\JoinColumn(name="email_status", referencedColumnName="address")
     */
    private EmailStatus $emailStatus;

    /**
     * The MessageObject that reported this complaint.
     *
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage", inversedBy="complaints")
     * @ORM\JoinColumn(name="mail_message", referencedColumnName="message_id")
     */
    private MailMessage $mailMessage;

    /**
     * The date and time at which the bounce was sent (in ISO8601 format).
     *
     * Note that this is the time at which the notification was sent by the ISP, and not the time at which it was
     * received by Amazon SES.
     *
     * @ORM\Column(name="complained_on", type="datetime")
     */
    private \DateTimeInterface $complainedOn;

    /**
     * A unique ID for the bounce.
     *
     * @ORM\Column(name="feedback_id", type="string")
     */
    private string $feedbackId;

    /**
     * The value of the User-Agent field from the feedback report.
     *
     * This indicates the name and version of the system that generated the report.
     *
     * @ORM\Column(name="user_agent")
     */
    private string $userAgent;

    /**
     * The value of the Feedback-Type field from the feedback report received from the ISP.
     *
     * This contains the type of feedback.
     *
     * @ORM\Column(name="complaint_feedback_type", type="string", nullable=true)
     */
    private ?string $complaintFeedbackType = null;

    /**
     * The value of the Arrival-Date or Received-Date field from the feedback report (in ISO8601 format).
     *
     * This field may be absent in the report (and therefore also absent in the JSON).
     *
     * @ORM\Column(name="arrival_date", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $arrivalDate = null;

    public static function create(EmailStatus $email, MailMessage $mailMessage, array $notification): Complaint
    {
        $complaint = (new self())
            ->setComplainedOn(new \DateTime($notification[self::COMPLAINT]['timestamp']))
            ->setFeedbackId($notification[self::COMPLAINT]['feedbackId'])
            ->setMailMessage($mailMessage)
            ->setEmailStatus($email);

        if (isset($notification[self::COMPLAINT]['userAgent'])) {
            $complaint->setUserAgent($notification[self::COMPLAINT]['userAgent']);
        }

        if (isset($notification[self::COMPLAINT]['complaintFeedbackType'])) {
            $complaint->setComplaintFeedbackType($notification[self::COMPLAINT]['complaintFeedbackType']);
        }

        if (isset($notification[self::COMPLAINT]['arrivalDate'])) {
            $complaint->setArrivalDate(new \DateTime($notification[self::COMPLAINT]['arrivalDate']));
        }

        return $complaint;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    public function getEmailStatus(): EmailStatus
    {
        return $this->emailStatus;
    }

    public function getComplainedOn(): \DateTimeInterface
    {
        return $this->complainedOn;
    }

    public function getFeedbackId(): string
    {
        return $this->feedbackId;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getComplaintFeedbackType(): ?string
    {
        return $this->complaintFeedbackType;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrivalDate;
    }

    private function setEmailStatus(EmailStatus $emailStatus): self
    {
        $this->emailStatus = $emailStatus;
        $emailStatus->addComplaint($this);

        return $this;
    }

    private function setMailMessage(MailMessage $mailMessage): self
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addComplaint($this);

        return $this;
    }

    private function setComplainedOn(\DateTimeInterface $complainedOn): self
    {
        $this->complainedOn = $complainedOn;

        return $this;
    }

    private function setFeedbackId(string $feedbackId): self
    {
        $this->feedbackId = $feedbackId;

        return $this;
    }

    private function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    private function setComplaintFeedbackType(string $complaintFeedbackType): self
    {
        $this->complaintFeedbackType = $complaintFeedbackType;

        return $this;
    }

    private function setArrivalDate(\DateTimeInterface $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }
}
