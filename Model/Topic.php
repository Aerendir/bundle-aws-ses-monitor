<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * A Topic entity.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class Topic
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $topicArn;

    /** @var null|string */
    protected $token;

    /**
     * @param $topicArn
     * @param null $token
     */
    public function __construct($topicArn, $token = null)
    {
        $this->topicArn = $topicArn;
        $this->token    = $token;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null|string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param string $topicArn
     *
     * @return $this
     */
    public function setTopicArn($topicArn)
    {
        $this->topicArn = $topicArn;

        return $this;
    }

    /**
     * @return string
     */
    public function getTopicArn()
    {
        return $this->topicArn;
    }
}
