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

/**
 * A Bounce Entity.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
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
     */
    private $id;

    /**
     * The MessageObject that reported this bounce.
     *
     * @var MailMessage
     */
    private $mailMessage;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * The date and time at which the bounce was sent (in ISO8601 format).
     *
     * Note that this is the time at which the notification was sent by the ISP, and not the time at which it was
     * received by Amazon SES.
     *
     * @var \DateTime
     */
    private $bouncedOn;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $subType;

    /**
     * A unique ID for the bounce.
     *
     * @var string
     */
    private $feedbackId;

    /**
     * The value of the Reporting-MTA field from the DSN.
     *
     * This is the value of the Message Transfer Authority (MTA) that attempted to perform the delivery, relay, or
     * gateway operation described in the DSN.
     *
     * @var string
     */
    private $reportingMta;

    /**
     * The value of the Action field from the DSN.
     *
     * This indicates the action performed by the Reporting-MTA as a result of its attempt to deliver the message to
     * this recipient.
     *
     * @var string
     */
    private $action;

    /**
     * The value of the EmailStatus field from the DSN.
     *
     * This is the per-recipient transport-independent status code that indicates the delivery status of the message.
     *
     * @var string
     */
    private $status;

    /**
     * The status code issued by the reporting MTA.
     *
     * This is the value of the Diagnostic-Code field from the DSN. This field may be absent in the DSN (and therefore
     * also absent in the JSON).
     *
     * @var string
     */
    private $diagnosticCode;

    /**
     * @var EmailStatus
     */
    private $emailStatus;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MailMessage
     */
    public function getMailMessage()
    {
        return $this->mailMessage;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return \DateTime
     */
    public function getBouncedOn()
    {
        return $this->bouncedOn;
    }

    /**
     * @return string
     */
    public function getFeedbackId()
    {
        return $this->feedbackId;
    }

    /**
     * @return string
     */
    public function getReportingMta()
    {
        return $this->reportingMta;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDiagnosticCode()
    {
        return $this->diagnosticCode;
    }

    /**
     * @return EmailStatus
     */
    public function getEmailStatus()
    {
        return $this->emailStatus;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return self::TYPE_PERMANENT === $this->type;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmailAddress($email)
    {
        $this->emailAddress = $email;

        return $this;
    }

    /**
     * @param MailMessage $mailMessage
     *
     * @return $this
     */
    public function setMailMessage(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;
        $this->mailMessage->addBounce($this);

        return $this;
    }

    /**
     * @param \DateTime $bouncedOn
     *
     * @return $this
     */
    public function setBouncedOn($bouncedOn)
    {
        $this->bouncedOn = $bouncedOn;

        return $this;
    }

    /**
     * @param bool $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param bool $subType
     *
     * @return $this
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * @param string $feedbackId
     *
     * @return $this
     */
    public function setFeedbackId($feedbackId)
    {
        $this->feedbackId = $feedbackId;

        return $this;
    }

    /**
     * @param string $reportingMta
     *
     * @return $this
     */
    public function setReportingMta($reportingMta)
    {
        $this->reportingMta = $reportingMta;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $diagnosticCode
     *
     * @return $this
     */
    public function setDiagnosticCode($diagnosticCode)
    {
        $this->diagnosticCode = $diagnosticCode;

        return $this;
    }
}
