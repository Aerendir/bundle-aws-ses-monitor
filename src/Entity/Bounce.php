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
 * A Bounce Entity.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * @ORM\Table(name="shq_aws_ses_monitor_bounces")
 * @ORM\Entity()
 */
class Bounce
{
    /** Hard bounces and subtypes */
    const TYPE_PERMANENT       = 'Permanent';
    const TYPE_PERM_GENERAL    = 'General';
    const TYPE_PERM_NOEMAIL    = 'NoEmail';
    const TYPE_PERM_SUPPRESSED = 'Suppressed';

    /** Soft bunces and subtypes */
    const TYPE_TRANSIENT            = 'Transient';
    const TYPE_TRANS_GENERAL        = 'General';
    const TYPE_TRANS_BOXFULL        = 'MailboxFull';
    const TYPE_TRANS_TOOLARGE       = 'MessageTooLarge';
    const TYPE_TRANS_CONTREJECTED   = 'ContentRejected';
    const TYPE_TRANS_ATTACHREJECTED = 'AttachmentRejected';

    /** Undetermined bounces */
    const TYPE_UNDETERMINED = 'Undetermined';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var EmailStatus
     * @ORM\ManyToOne(targetEntity="EmailStatus", inversedBy="bounces", cascade={"persist"})
     * @ORM\JoinColumn(name="email", referencedColumnName="address")
     */
    private $email;

    /**
     * The MessageObject that reported this bounce.
     *
     * @var MailMessage
     *
     * @ORM\ManyToOne(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage", inversedBy="bounces")
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
            ->setBouncedOn(new \DateTime($notification['bounce']['timestamp']))
            ->setType(($notification['bounce']['bounceType']))
            ->setSubType(($notification['bounce']['bounceSubType']))
            ->setFeedbackId($notification['bounce']['feedbackId'])
            ->setMailMessage($mailMessage)
            ->setEmail($email);

        if (isset($notification['bounce']['reportingMta'])) {
            $bounce->setReportingMta($notification['bounce']['reportingMta']);
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
    public function getEmail(): EmailStatus
    {
        return $this->email;
    }

    /**
     * @return MailMessage
     */
    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    /**
     * @return \DateTime
     */
    public function getBouncedOn(): \DateTime
    {
        return $this->bouncedOn;
    }

    /**
     * @return string
     */
    public function getFeedbackId(): string
    {
        return $this->feedbackId;
    }

    /**
     * @return string|null
     */
    public function getReportingMta(): ?string
    {
        return $this->reportingMta;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getDiagnosticCode(): ?string
    {
        return $this->diagnosticCode;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSubType(): string
    {
        return $this->subType;
    }

    /**
     * @return bool
     */
    public function isPermanent(): bool
    {
        return self::TYPE_PERMANENT === $this->type;
    }

    /**
     * @param EmailStatus $email
     *
     * @return Bounce
     */
    private function setEmail(EmailStatus $email): Bounce
    {
        $this->email = $email;
        $email->addBounce($this);

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Bounce
     */
    private function setType(string $type): Bounce
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $subType
     *
     * @return Bounce
     */
    private function setSubType(string $subType): Bounce
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * @param string $feedbackId
     *
     * @return Bounce
     */
    private function setFeedbackId(string $feedbackId): Bounce
    {
        $this->feedbackId = $feedbackId;

        return $this;
    }

    /**
     * @param string $reportingMta
     *
     * @return Bounce
     */
    private function setReportingMta(string $reportingMta): Bounce
    {
        $this->reportingMta = $reportingMta;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return Bounce
     */
    private function setAction(string $action): Bounce
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return Bounce
     */
    private function setStatus(string $status): Bounce
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $diagnosticCode
     *
     * @return Bounce
     */
    private function setDiagnosticCode(string $diagnosticCode): Bounce
    {
        $this->diagnosticCode = $diagnosticCode;

        return $this;
    }

    /**
     * @param \DateTime $bouncedOn
     *
     * @return Bounce
     */
    private function setBouncedOn(\DateTime $bouncedOn): Bounce
    {
        $this->bouncedOn = $bouncedOn;

        return $this;
    }

    /**
     * @param MailMessage $mailMessage
     *
     * @return Bounce
     */
    private function setMailMessage(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addBounce($this);

        return $this;
    }
}
