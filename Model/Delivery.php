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
    private $mail;

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
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
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
     * @param Mail $mail
     *
     * @return $this
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;

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
