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
 * A MailMessage Entity.
 *
 * This is called MailObject by Amazon.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#mail-object
 *
 * @ORM\Table(name="shq_aws_ses_monitor_messages")
 * @ORM\Entity()
 */
class MailMessage
{
    /**
     * A unique ID that Amazon SES assigned to the message.
     *
     * Amazon SES returned this value to you when you sent the message.
     * This message ID was assigned by Amazon SES. You can find the message ID of the original
     * email in the headers and commonHeaders fields of the mail object.
     *
     * @ORM\Column(name="message_id", type="string")
     * @ORM\Id
     */
    private string $messageId;

    /**
     * The time at which the original message was sent (in ISO8601 format).
     *
     * Formerly "timestamp".
     *
     * @ORM\Column(name="sent_on", type="datetime")
     */
    private \DateTimeInterface $sentOn;

    /**
     * The email address from which the original message was sent (the envelope MAIL FROM address).
     *
     * Formerly "source".
     *
     * @ORM\Column(name="sent_from", type="string")
     */
    private string $sentFrom;

    /**
     * The Amazon Resource Name (ARN) of the identity that was used to send the email.
     *
     * In the case of sending authorization, the sourceArn is the ARN of the identity that the identity owner authorized
     * the delegate sender to use to send the email. For more information about sending authorization, see Using Sending
     * Authorization.
     *
     * @ORM\Column(name="source_arn", type="string")
     */
    private string $sourceArn;

    /**
     * The AWS account ID of the account that was used to send the email.
     *
     * In the case of sending authorization, the sendingAccountId is the delegate sender's account ID.
     *
     * @ORM\Column(name="sending_account_id", type="string")
     */
    private string $sendingAccountId;

    /**
     * A list of the email's original headers. Each header in the list has a name field and a value field.
     *
     * Any message ID within the headers field is from the original message that you passed to Amazon SES. The message
     * ID that Amazon SES subsequently assigned to the message is in the messageId field of the mail object.
     *
     * (Only present if the notification settings include the original email headers.)
     *
     * @ORM\Column(name="headers", type="text", nullable=true)
     */
    private ?string $headers = null;

    /**
     * A list of the email's original, commonly used headers.
     *
     * Each header in the list has a name field and a value field.
     * Any message ID within the commonHeaders field is from the original message that you passed to Amazon SES. The
     * message ID that Amazon SES subsequently assigned to the message is in the messageId field of the mail object.
     *
     * (Only present if the notification settings include the original email headers.)
     *
     * @ORM\Column(name="common_headers", type="text", nullable=true)
     */
    private ?string $commonHeaders = null;

    /** @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce", mappedBy="mailMessage", cascade={"persist"})
     * @var Collection<int, Bounce>|Bounce[] */
    private Collection $bounces;

    /** @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint", mappedBy="mailMessage", cascade={"persist"})
     * @var Collection<int, Complaint>|Complaint[] */
    private Collection $complaints;

    /** @ORM\OneToMany(targetEntity="SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery", mappedBy="mailMessage", cascade={"persist"})
     * @var Collection<int, Delivery>|Delivery[] */
    private Collection $deliveries;

    /**
     * MailMessage constructor.
     */
    public function __construct()
    {
        $this->bounces    = new ArrayCollection();
        $this->complaints = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
    }

    public static function create(array $mailMessageData): MailMessage
    {
        $mailMessage = new self();
        $mailMessage->setMessageId($mailMessageData['messageId'])
                    ->setSentOn(new \DateTime($mailMessageData['timestamp']))
                    ->setSentFrom($mailMessageData['source'])
                    ->setSourceArn($mailMessageData['sourceArn'])
                    ->setSendingAccountId($mailMessageData['sendingAccountId']);

        if (isset($mailMessageData['headers'])) {
            $mailMessage->setHeaders($mailMessageData['headers']);
        }

        if (isset($mailMessageData['commonHeaders'])) {
            $mailMessage->setCommonHeaders($mailMessageData['commonHeaders']);
        }

        return $mailMessage;
    }

    public function addBounce(Bounce $bounce): self
    {
        $this->bounces->add($bounce);

        return $this;
    }

    public function addComplaint(Complaint $complaint): self
    {
        $this->complaints->add($complaint);

        return $this;
    }

    public function addDelivery(Delivery $delivery): self
    {
        $this->deliveries->add($delivery);

        return $this;
    }

    public function getBounces(): Collection
    {
        return $this->bounces;
    }

    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getSentOn(): \DateTimeInterface
    {
        return $this->sentOn;
    }

    public function getSentFrom(): string
    {
        return $this->sentFrom;
    }

    public function getSourceArn(): string
    {
        return $this->sourceArn;
    }

    public function getSendingAccountId(): string
    {
        return $this->sendingAccountId;
    }

    public function getHeaders(): ?string
    {
        return $this->headers;
    }

    public function getCommonHeaders(): ?string
    {
        return $this->commonHeaders;
    }

    /**
     * @internal
     */
    public function setMessageId(string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * @internal
     */
    public function setSentOn(\DateTimeInterface $sentOn): self
    {
        $this->sentOn = $sentOn;

        return $this;
    }

    /**
     * @internal
     */
    public function setSentFrom(string $sentFrom): self
    {
        $this->sentFrom = $sentFrom;

        return $this;
    }

    /**
     * @internal
     */
    public function setSourceArn(string $sourceArn): self
    {
        $this->sourceArn = $sourceArn;

        return $this;
    }

    /**
     * @internal
     */
    public function setSendingAccountId(string $sendingAccountId): self
    {
        $this->sendingAccountId = $sendingAccountId;

        return $this;
    }

    /**
     * @internal
     */
    public function setHeaders(string $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @internal
     */
    public function setCommonHeaders(string $commonHeaders): self
    {
        $this->commonHeaders = $commonHeaders;

        return $this;
    }
}
