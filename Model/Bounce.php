<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * A Bounce Entity.
 *
 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-contents.html#bounce-object
 */
class Bounce
{
    /** Hard bounces and subtypes */
    const TYPE_PERMANENT            = 'Permanent';
    const TYPE_PERM_GENERAL         = 'General';
    const TYPE_PERM_NOEMAIL         = 'NoEmail';
    const TYPE_PERM_SUPPRESSED      = 'Suppressed';

    /** Soft bunces and subtypes */
    const TYPE_TRANSIENT            = 'Transient';
    const TYPE_TRANS_GENERAL        = 'General';
    const TYPE_TRANS_BOXFULL        = 'MailboxFull';
    const TYPE_TRANS_TOOLARGE       = 'MessageTooLarge';
    const TYPE_TRANS_CONTREJECTED   = 'ContentRejected';
    const TYPE_TRANS_ATTACHREJECTED = 'AttachmentRejected';

    /** Undetermined bounces */
    const TYPE_UNDETERMINED         = 'Undetermined';

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var
     */
    private $mail;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $lastTimeBounce;

    /**
     * @var int
     */
    private $bounceCount;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $subType;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = mb_strtolower($email);
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
     * @return int
     */
    public function getBounceCount()
    {
        return $this->bounceCount;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \DateTime
     */
    public function getLastTimeBounce()
    {
        return $this->lastTimeBounce;
    }

    /**
     * @return $this
     */
    public function incrementBounceCounter()
    {
        ++$this->bounceCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return self::TYPE_PERMANENT === $this->type;
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
}
