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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager;

use Aws\Result;
use Aws\Ses\SesClient;

/**
 * Manages the interaction with AWS SES.
 */
final class SesManager
{
    /** @var string */
    private const IDENTITY = 'Identity';

    private SesClient $client;

    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    /**
     * @ codeCoverageIgnore
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
     * @param string $notificationType The type of notification
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitynotificationtopic
     *
     * @ codeCoverageIgnore
     */
    public function setTopic(string $identity, string $notificationType, string $topicArn): void
    {
        $this->client->setIdentityNotificationTopic(
            [
                self::IDENTITY     => $identity,
                'NotificationType' => $notificationType,
                'SnsTopic'         => $topicArn,
            ]
        );
    }

    /**
     * @ codeCoverageIgnore
     */
    public function configureDkim(string $identity, bool $enabled): void
    {
        $this->client->setIdentityDkimEnabled([
            self::IDENTITY => $identity,
            'DkimEnabled'  => $enabled,
        ]);
    }

    /**
     * @ codeCoverageIgnore
     */
    public function configureFeedbackForwarding(string $identity, bool $enabled): void
    {
        $this->client->setIdentityFeedbackForwardingEnabled([
            self::IDENTITY      => $identity,
            'ForwardingEnabled' => $enabled,
        ]);
    }

    /**
     * @ codeCoverageIgnore
     */
    public function configureFromDomain(string $identity, ?string $domain, string $onMxFailure): void
    {
        $this->client->setIdentityMailFromDomain([
            self::IDENTITY        => $identity,
            'BehaviorOnMXFailure' => $onMxFailure,
            'MailFromDomain'      => $domain,
        ]);
    }

    /**
     * @ codeCoverageIgnore
     */
    public function verifyDomainIdentity(string $identity): string
    {
        $result = $this->client->verifyDomainIdentity(['Domain' => $identity]);

        return $result->get('VerificationToken');
    }

    /**
     * @ codeCoverageIgnore
     */
    public function verifyEmailIdentity(string $identity): void
    {
        $this->client->verifyEmailIdentity(['EmailAddress' => $identity]);
    }

    public function getClient(): SesClient
    {
        return $this->client;
    }
}
