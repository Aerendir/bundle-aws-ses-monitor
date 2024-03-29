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

use Doctrine\ORM\Mapping as ORM;

/**
 * A Topic entity.
 *
 * @ORM\Table(name="shq_aws_ses_monitor_topics")
 * @ORM\Entity()
 */
class Topic
{
    /**
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private int $id;

    /** @ORM\Column(name="name", type="string", length=296) */
    private string $name;

    /** @ORM\Column(name="arn", type="string", length=296) */
    private string $arn;

    /**
     * Topic constructor.
     */
    public function __construct(string $topicName, string $topicArn)
    {
        $this->name = $topicName;
        $this->arn  = $topicArn;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArn(): string
    {
        return $this->arn;
    }
}
