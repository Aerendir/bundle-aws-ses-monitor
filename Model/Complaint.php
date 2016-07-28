<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Represents a Complaint.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#complaint-object
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class Complaint
{
    /** Indicates unsolicited email or some other kind of email abuse. */
    const TYPE_ABUSE = 'abuse';

    /** EmailStatus authentication failure report. */
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
     * @var int $id
     */
    private $id;

    /**
     * The MessageObject that reported this complaint.
     *
     * @var MailMessage $mailMessage
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
    private $complainedOn;

    /**
     * A unique ID for the bounce.
     *
     * @var string $feedbackId
     */
    private $feedbackId;

    /**
     * The value of the User-Agent field from the feedback report.
     *
     * This indicates the name and version of the system that generated the report.
     *
     * @var string $userAgent
     */
    private $userAgent;

    /**
     * The value of the Feedback-Type field from the feedback report received from the ISP.
     *
     * This contains the type of feedback.
     *
     * @var string $complaintFeedbackType
     */
    private $complaintFeedbackType;

    /**
     * The value of the Arrival-Date or Received-Date field from the feedback report (in ISO8601 format).
     *
     * This field may be absent in the report (and therefore also absent in the JSON).
     *
     * @var string $arrivalDate
     */
    private $arrivalDate;

    /**
     * @var EmailStatus $emailStatus
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
    public function getComplainedOn()
    {
        return $this->complainedOn;
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
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getComplaintFeedbackType()
    {
        return $this->complaintFeedbackType;
    }

    /**
     * @return string
     */
    public function getArrivalDate()
    {
        return $this->arrivalDate;
    }

    /**
     * @return EmailStatus
     */
    public function getEmailStatus()
    {
        return $this->emailStatus;
    }

    /**
     * @param string $email
     */
    public function setEmailAddress($email)
    {
        $this->emailAddress = $email;
    }

    /**
     * @param MailMessage $mailMessage
     *
     * @return $this
     */
    public function setMailMessage(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;

        return $this;
    }

    /**
     * @param \DateTime $complainedOn
     *
     * @return $this
     */
    public function setComplainedOn($complainedOn)
    {
        $this->complainedOn = $complainedOn;

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
     * @param string $userAgent
     *
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @param string $complaintFeedbackType
     *
     * @return $this
     */
    public function setComplaintFeedbackType($complaintFeedbackType)
    {
        $this->complaintFeedbackType = $complaintFeedbackType;

        return $this;
    }

    /**
     * @param \DateTime $arrivalDate
     *
     * @return $this
     */
    public function setArrivalDate(\DateTime $arrivalDate)
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }
}
