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

use Doctrine\ORM\Mapping as ORM;

/**
 * A Topic entity.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @ORM\Table(name="shq_aws_ses_monitor_topics")
 * @ORM\Entity()
 */
class Topic
{
    /**
     * @var int
     * @ORM\Column(name="email", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="topic_arn", type="string", length=296)
     */
    private $topicArn;

    /**
     * @var string|null
     * @ORM\Column(name="token", type="string", length=1024, nullable=true)
     */
    private $token;

    /**
     * @param string      $topicArn
     * @param string|null $token
     */
    public function __construct(string $topicArn, ?string $token = null)
    {
        $this->setTopicArn($topicArn);
        $this->setToken($token);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTopicArn(): string
    {
        return $this->topicArn;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $topicArn
     *
     * @return Topic
     */
    public function setTopicArn(string $topicArn): Topic
    {
        $this->topicArn = $topicArn;

        return $this;
    }

    /**
     * @param string|null $token
     *
     * @return Topic
     */
    public function setToken(?string $token): Topic
    {
        $this->token = $token;

        return $this;
    }
}
