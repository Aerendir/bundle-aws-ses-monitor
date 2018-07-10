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

    /** @var string|null */
    protected $token;

    /**
     * @param $topicArn
     * @param null $token
     */
    public function __construct($topicArn, $token = null)
    {
        $this->setTopicArn($topicArn);
        $this->setToken($token);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string|null $token
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
