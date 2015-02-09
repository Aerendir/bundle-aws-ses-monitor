<?php
namespace Shivas\BouncerBundle\Model;

class Bounce
{
    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var \DateTime
     */
    protected $lastTimeBounce;

    /**
     * @var int
     */
    protected $bounceCount;

    /**
     * @var bool
     */
    protected $permanent;

    public function __construct($emailAddress, $lastTimeBounce, $bounceCount = 1 , $permanent = false)
    {
        $this->setEmailAddress($emailAddress);
        $this->lastTimeBounce = $lastTimeBounce;
        $this->bounceCount = $bounceCount;
        $this->permanent = $permanent;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     * @return $this
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = mb_strtolower($emailAddress);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastTimeBounce()
    {
        return $this->lastTimeBounce;
    }

    /**
     * @param \DateTime $lastTimeBounce
     * @return $this
     */
    public function setLastTimeBounce($lastTimeBounce)
    {
        $this->lastTimeBounce = $lastTimeBounce;
        return $this;
    }

    /**
     * @return int
     */
    public function getBounceCount()
    {
        return $this->bounceCount;
    }

    /**
     * @param int $bounceCount
     * @return $this
     */
    public function setBounceCount($bounceCount)
    {
        $this->bounceCount = $bounceCount;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param boolean $permanent
     * @return $this
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;
        return $this;
    }

    public function incrementBounceCounter()
    {
        $this->bounceCount++;
        return $this;
    }
}
