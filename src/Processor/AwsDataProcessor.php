<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor;

use Aws\Result;

/**
 * Collects information from AWS SES and SNS and transforms them in a unique big array.
 *
 * @internal
 */
final class AwsDataProcessor
{
    /**
     * @var string
     */
    public const ACCOUNT       = 'account';

    /**
     * @var string
     */
    public const IDENTITIES    = 'identities';

    /**
     * @var string
     */
    public const SUBSCRIPTIONS = 'subscriptions';

    /**
     * @var string
     */
    public const TOPICS        = 'topics';

    /**
     * @var string
     */
    private const MAIL_FROM = 'mail_from';

    /**
     * @var string
     */
    private const NOTIFICATIONS = 'notifications';

    /**
     * @var string
     */
    private const INCLUDE_HEADERS = 'include_headers';

    /**
     * @var string
     */
    private const TOPIC = 'topic';

    /**
     * @var string
     */
    private const SUBSCRIPTION_ARN = 'SubscriptionArn';

    /**
     * @var string
     */
    private const TOPIC_ARN = 'TopicArn';

    /** @var array */
    private $data = [];

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processAccountSendingEnabled(Result $result): void
    {
        $this->data[self::ACCOUNT]['enabled'] = $result->get('Enabled');
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processAccountSendQuota(Result $result): void
    {
        $this->data[self::ACCOUNT]['quota'] = [
            'max_24_hour_send'   => $result->get('Max24HourSend'),
            'max_send_rate'      => $result->get('MaxSendRate'),
            'sent_last_24_hours' => $result->get('SentLast24Hours'),
        ];
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processAccountSendStatistics(Result $result): void
    {
        $this->data[self::ACCOUNT]['stats'] = $result->get('SendDataPoints');
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processIdentitiesDkimAttributes(Result $result): void
    {
        foreach ($result->get('DkimAttributes') as $identity => $attribute) {
            $this->data[self::IDENTITIES][$identity]['dkim'] = [
                'enabled'             => $attribute['DkimEnabled'],
                'verification_status' => $attribute['DkimVerificationStatus'],
            ];

            if (isset($attribute['DkimTokens'])) {
                $this->data[self::IDENTITIES][$identity]['dkim']['tokens'] = $attribute['DkimTokens'];
            }
        }
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processIdentitiesMailFromDomainAttributes(Result $result): void
    {
        foreach ($result->get('MailFromDomainAttributes') as $identity => $attribute) {
            $this->data[self::IDENTITIES][$identity][self::MAIL_FROM]['on_mx_failure'] = $attribute['BehaviorOnMXFailure'];

            if (isset($attribute['MailFromDomainStatus'])) {
                $this->data[self::IDENTITIES][$identity][self::MAIL_FROM]['status'] = $attribute['MailFromDomainStatus'];
            }

            if (isset($attribute['MailFromDomain'])) {
                $this->data[self::IDENTITIES][$identity][self::MAIL_FROM]['domain'] = $attribute['MailFromDomain'];
            }
        }
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processIdentitiesNotificationAttributes(Result $result): void
    {
        foreach ($result->get('NotificationAttributes') as $identity => $attribute) {
            $this->data[self::IDENTITIES][$identity][self::NOTIFICATIONS] = [
                'forwarding_enabled' => $attribute['ForwardingEnabled'],
                'bounces'            => [self::INCLUDE_HEADERS => $attribute['HeadersInBounceNotificationsEnabled']],
                'complaints'         => [self::INCLUDE_HEADERS => $attribute['HeadersInComplaintNotificationsEnabled']],
                'deliveries'         => [self::INCLUDE_HEADERS => $attribute['HeadersInDeliveryNotificationsEnabled']],
            ];

            // Thoses fields are not present if the identity was not set to send notification to a SNS topic
            if (isset($attribute['BounceTopic'])) {
                $this->data[self::IDENTITIES][$identity][self::NOTIFICATIONS]['bounces'][self::TOPIC] = $attribute['BounceTopic'];
            }

            if (isset($attribute['ComplaintTopic'])) {
                $this->data[self::IDENTITIES][$identity][self::NOTIFICATIONS]['complaints'][self::TOPIC] = $attribute['ComplaintTopic'];
            }

            if (isset($attribute['DeliveryTopic'])) {
                $this->data[self::IDENTITIES][$identity][self::NOTIFICATIONS]['deliveries'][self::TOPIC] = $attribute['DeliveryTopic'];
            }
        }
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processIdentitiesVerificationAttributes(Result $result): void
    {
        foreach ($result->get('VerificationAttributes') as $identity => $attribute) {
            $this->data[self::IDENTITIES][$identity]['verification']['status'] = $attribute['VerificationStatus'];

            if (isset($attribute['VerificationToken'])) {
                $this->data[self::IDENTITIES][$identity]['verification']['token'] = $attribute['VerificationToken'];
            }
        }
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processSubscriptions(Result $result): void
    {
        foreach ($result->get('Subscriptions') as $subscription) {
            if ('PendingConfirmation' !== $subscription[self::SUBSCRIPTION_ARN]) {
                $this->data[self::SUBSCRIPTIONS][$subscription[self::SUBSCRIPTION_ARN]] = [
                    'subscription_arn' => $subscription[self::SUBSCRIPTION_ARN],
                    'owner'            => $subscription['Owner'],
                    'protocol'         => $subscription['Protocol'],
                    'endpoint'         => $subscription['Endpoint'],
                    'topic_arn'        => $subscription[self::TOPIC_ARN],
                ];
            }
        }
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processSubscriptionAttributes(Result $result): void
    {
        $attributes = $result->get('Attributes');

        $this->data[self::SUBSCRIPTIONS][$attributes[self::SUBSCRIPTION_ARN]]['raw_message_delivery']      = $attributes['RawMessageDelivery'];
        $this->data[self::SUBSCRIPTIONS][$attributes[self::SUBSCRIPTION_ARN]]['effective_delivery_policy'] = $attributes['EffectiveDeliveryPolicy'];
        $this->data[self::SUBSCRIPTIONS][$attributes[self::SUBSCRIPTION_ARN]]['confirmation']              = [
            'pending'           => $attributes['PendingConfirmation'],
            'was_authenticated' => $attributes['ConfirmationWasAuthenticated'],
        ];
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processTopics(Result $result): void
    {
        foreach ($result->get('Topics') as $topic) {
            $this->data[self::TOPICS][$topic[self::TOPIC_ARN]] = [];
        }
    }

    /**
     * @param Result $result
     *
     * @internal
     */
    public function processTopicAttributes(Result $result): void
    {
        $attributes = $result->get('Attributes');

        $this->data[self::TOPICS][$attributes[self::TOPIC_ARN]] = [
            'arn'                       => $attributes[self::TOPIC_ARN],
            'display_name'              => $attributes['DisplayName'],
            'policy'                    => $attributes['Policy'],
            'owner'                     => $attributes['Owner'],
            'effective_delivery_policy' => $attributes['EffectiveDeliveryPolicy'],
            'subscriptions'             => [
                'confirmed' => $attributes['SubscriptionsConfirmed'],
                'deleted'   => $attributes['SubscriptionsDeleted'],
                'pending'   => $attributes['SubscriptionsPending'],
            ],
        ];
    }

    /**
     * Returns the processed data.
     *
     * On call, resets the internal array.
     *
     * @return array
     *
     * @internal
     */
    public function getProcessedData(): array
    {
        $data       = $this->data;
        $this->data = [];

        return $data;
    }
}
