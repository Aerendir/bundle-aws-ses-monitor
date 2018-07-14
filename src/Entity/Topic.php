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
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @ORM\Table(name="shq_aws_ses_monitor_topics")
 * @ORM\Entity()
 */
class Topic
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", unique=true)
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
     * @param string $topicArn
     */
    public function __construct(string $topicArn)
    {
        $this->setTopicArn($topicArn);
    }

    /**
     * @return int
     * @codeCoverageIgnore
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
     * @param string $topicArn
     *
     * @return Topic
     */
    private function setTopicArn(string $topicArn): Topic
    {
        $this->topicArn = $topicArn;

        return $this;
    }
}
