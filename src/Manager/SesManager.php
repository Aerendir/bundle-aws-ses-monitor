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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager;

use Aws\Result;
use Aws\Ses\SesClient;

/**
 * Manages the interaction with AWS SES.
 */
class SesManager
{
    /** @var \Aws\Ses\SesClient $client */
    private $client;

    /**
     * @param SesClient $client
     */
    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return Result
     */
    public function listIdentities(): Result
    {
        return $this->client->listIdentities();
    }

    /**
     * For the given Identity, sets the topic to which send notifications.
     *
     * Given an identity (an email address or a domain), sets the Amazon Simple
     * Notification Service (Amazon SNS) topic to which Amazon SES will publish
     * bounce, complaint, and/or delivery notifications for emails sent with
     * that identity as the Source.
     *
     * @param string $identity
     * @param string $notificationType The type of notification
     * @param string $topicArn
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitynotificationtopic
     */
    public function setTopic(string $identity, string $notificationType, string $topicArn): void
    {
        $this->client->setIdentityNotificationTopic(
                [
                    'Identity'         => $identity,
                    'NotificationType' => $notificationType,
                    'SnsTopic'         => $topicArn,
                ]
            );
    }
}
