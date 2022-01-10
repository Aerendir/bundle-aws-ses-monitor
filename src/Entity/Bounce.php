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

use Doctrine\ORM\Mapping as ORM;

/**
 * A Bounce Entity.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
 *
 * @ORM\Table(name="shq_aws_ses_monitor_bounces")
 * @ORM\Entity()
 */
class Bounce
{
    /** Hard bounces and subtypes.
     * @var string */
    public const TYPE_PERMANENT = 'Permanent';

    /**
     * @var string
     */
    public const TYPE_PERM_GENERAL = 'General';

    /**
     * @var string
     */
    public const TYPE_PERM_NOEMAIL = 'NoEmail';

    /**
     * @var string
     */
    public const TYPE_PERM_SUPPRESSED = 'Suppressed';

    /** Soft bunces and subtypes.
     * @var string */
    public const TYPE_TRANSIENT = 'Transient';

    /**
     * @var string
     */
    public const TYPE_TRANS_GENERAL = 'General';

    /**
     * @var string
     */
    public const TYPE_TRANS_BOXFULL = 'MailboxFull';

    /**
     * @var string
     */
    public const TYPE_TRANS_TOOLARGE = 'MessageTooLarge';

    /**
     * @var string
     */
    public const TYPE_TRANS_CONTREJECTED = 'ContentRejected';

    /**
     * @var string
     */
    public const TYPE_TRANS_ATTACHREJECTED = 'AttachmentRejected';

    /** Undetermined bounces.
     * @var string */
    public const TYPE_UNDETERMINED = 'Undetermined';

    /**
     * @var string
     */
    private const BOUNCE = 'bounce';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var EmailStatus
     * @ORM\ManyToOne(targetEntity="EmailStatus", inversedBy="bounces", cascade={"persist"})
     * @ORM\JoinColumn(name="email_status", referencedColumnName="address")
     */
    private $emailStatus;

    /**
     * The MessageObject that reported this bounce.
     *
     * @var MailMessage
     *
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage", inversedBy="bounces")
     * @ORM\JoinColumn(name="mail_message", referencedColumnName="message_id")
     */
    private $mailMessage;

    /**
     * The date and time at which the bounce was sent (in ISO8601 format).
     *
     * Note that this is the time at which the notification was sent by the ISP, and not the time at which it was
     * received by Amazon SES.
     *
     * @var \DateTimeInterface
     * @ORM\Column(name="bounced_on", type="datetime")
     */
    private $bouncedOn;

    /**
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="sub_type", type="string")
     */
    private $subType;

    /**
     * A unique ID for the bounce.
     *
     * @var string
     * @ORM\Column(name="feedback_id", type="string")
     */
    private $feedbackId;

    /**
     * The value of the Reporting-MTA field from the DSN.
     *
     * This is the value of the Message Transfer Authority (MTA) that attempted to perform the delivery, relay, or
     * gateway operation described in the DSN.
     *
     * @var string|null
     * @ORM\Column(name="reporting_mta", type="string", nullable=true)
     */
    private $reportingMta;

    /**
     * The value of the Action field from the DSN.
     *
     * This indicates the action performed by the Reporting-MTA as a result of its attempt to deliver the message to
     * this recipient.
     *
     * @var string|null
     * @ORM\Column(name="action", type="text", nullable=true)
     */
    private $action;

    /**
     * The value of the EmailStatus field from the DSN.
     *
     * This is the per-recipient transport-independent status code that indicates the delivery status of the message.
     *
     * @var string|null
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;

    /**
     * The status code issued by the reporting MTA.
     *
     * This is the value of the Diagnostic-Code field from the DSN. This field may be absent in the DSN (and therefore
     * also absent in the JSON).
     *
     * @var string|null
     * @ORM\Column(name="diagnostic_code", type="text", nullable=true)
     */
    private $diagnosticCode;

    /**
     * @param EmailStatus $email
     * @param MailMessage $mailMessage
     * @param array       $bouncedRecipient
     * @param array       $notification
     *
     * @return Bounce
     */
    public static function create(EmailStatus $email, MailMessage $mailMessage, array $bouncedRecipient, array $notification): Bounce
    {
        $bounce = (new self())
            ->setBouncedOn(new \DateTime($notification[self::BOUNCE]['timestamp']))
            ->setType(($notification[self::BOUNCE]['bounceType']))
            ->setSubType(($notification[self::BOUNCE]['bounceSubType']))
            ->setFeedbackId($notification[self::BOUNCE]['feedbackId'])
            ->setMailMessage($mailMessage)
            ->setEmailStatus($email);

        if (isset($notification[self::BOUNCE]['reportingMta'])) {
            $bounce->setReportingMta($notification[self::BOUNCE]['reportingMta']);
        }

        if (isset($bouncedRecipient['action'])) {
            $bounce->setAction($bouncedRecipient['action']);
        }

        if (isset($bouncedRecipient['status'])) {
            $bounce->setStatus($bouncedRecipient['status']);
        }

        if (isset($bouncedRecipient['diagnosticCode'])) {
            $bounce->setDiagnosticCode($bouncedRecipient['diagnosticCode']);
        }

        return $bounce;
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return EmailStatus
     */
    public function getEmailStatus(): EmailStatus
    {
        return $this->emailStatus;
    }

    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    public function getBouncedOn(): \DateTimeInterface
    {
        return $this->bouncedOn;
    }

    public function getFeedbackId(): string
    {
        return $this->feedbackId;
    }

    public function getReportingMta(): ?string
    {
        return $this->reportingMta;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getDiagnosticCode(): ?string
    {
        return $this->diagnosticCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function isPermanent(): bool
    {
        return self::TYPE_PERMANENT === $this->type;
    }

    private function setEmailStatus(EmailStatus $emailStatus): self
    {
        $this->emailStatus = $emailStatus;
        $emailStatus->addBounce($this);

        return $this;
    }

    private function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    private function setSubType(string $subType): self
    {
        $this->subType = $subType;

        return $this;
    }

    private function setFeedbackId(string $feedbackId): self
    {
        $this->feedbackId = $feedbackId;

        return $this;
    }

    private function setReportingMta(string $reportingMta): self
    {
        $this->reportingMta = $reportingMta;

        return $this;
    }

    private function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    private function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    private function setDiagnosticCode(string $diagnosticCode): self
    {
        $this->diagnosticCode = $diagnosticCode;

        return $this;
    }

    private function setBouncedOn(\DateTimeInterface $bouncedOn): self
    {
        $this->bouncedOn = $bouncedOn;

        return $this;
    }

    private function setMailMessage(MailMessage $mailMessage): self
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addBounce($this);

        return $this;
    }
}
