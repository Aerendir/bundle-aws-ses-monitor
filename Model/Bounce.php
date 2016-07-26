<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * A Bounce Entity.
 */
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

    /**
     * @param $emailAddress
     */
    public function __construct($emailAddress)
    {
        $this->emailAddress = mb_strtolower($emailAddress);
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
    public function getLastTimeBounce()
    {
        return $this->lastTimeBounce;
    }

    /**
     * @param \DateTime $lastTimeBounce
     *
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
     *
     * @return $this
     */
    public function setBounceCount($bounceCount)
    {
        $this->bounceCount = $bounceCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param bool $permanent
     *
     * @return $this
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * @return $this
     */
    public function incrementBounceCounter()
    {
        ++$this->bounceCount;

        return $this;
    }
}
