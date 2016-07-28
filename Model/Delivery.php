<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Represents a Delivery.
 */
class Delivery
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var
     */
    private $mailMessage;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var \DateTime
     */
    private $deliveryTime;

    /**
     * @param $emailAddress
     */
    public function __construct($emailAddress)
    {
        $this->emailAddress = mb_strtolower($emailAddress);
    }

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
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
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
     * @param \DateTime $deliveryTime
     *
     * @return $this
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }
}
