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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor;

use Aws\Result;

/**
 * Collects information from AWS SES and SNS and transforms them in a unique big array.
 */
class AwsDataProcessor
{
    const ACCOUNT       = 'account';
    const IDENTITIES    = 'identities';
    const SUBSCRIPTIONS = 'subscriptions';
    const TOPICS        = 'topics';

    /** @var array */
    private $data = [];

    /**
     * @param Result $result
     */
    public function processAccountSendingEnabled(Result $result): void
    {
        $this->data[self::ACCOUNT]['enabled'] = $result->get('Enabled');
    }

    /**
     * @param Result $result
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
     */
    public function processAccountSendStatistics(Result $result): void
    {
        $this->data[self::ACCOUNT]['stats'] = $result->get('SendDataPoints');
    }

    /**
     * @param Result $result
     */
    public function processIdentities(Result $result): void
    {
        foreach ($result->get('Identities') as $identity) {
            $this->data[self::IDENTITIES][$identity] = [];
        }
    }

    /**
     * @param Result $result
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
     */
    public function processIdentitiesMailFromDomainAttributes(Result $result): void
    {
        foreach ($result->get('MailFromDomainAttributes') as $identity => $attribute) {
            $this->data[self::IDENTITIES][$identity]['mail_from']['on_mx_failure'] = $attribute['BehaviorOnMXFailure'];
        }
    }

    /**
     * @param Result $result
     */
    public function processIdentitiesNotificationAttributes(Result $result): void
    {
        foreach ($result->get('NotificationAttributes') as $identity => $attribute) {
            $this->data[self::IDENTITIES][$identity]['notifications'] = [
                'forwarding_enabled' => $attribute['ForwardingEnabled'],
                'bounces'            => [
                    'topic'           => $attribute['BounceTopic'],
                    'include_headers' => $attribute['HeadersInBounceNotificationsEnabled'],
                ],
                'complaints' => [
                    'topic'           => $attribute['ComplaintTopic'],
                    'include_headers' => $attribute['HeadersInComplaintNotificationsEnabled'],
                ],
                'deliveries' => [
                    'topic'           => $attribute['DeliveryTopic'],
                    'include_headers' => $attribute['HeadersInDeliveryNotificationsEnabled'],
                ],
            ];
        }
    }

    /**
     * @param Result $result
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
     */
    public function processSubscriptions(Result $result): void
    {
        foreach ($result->get('Subscriptions') as $subscription) {
            $this->data[self::SUBSCRIPTIONS][$subscription['SubscriptionArn']] = [
                'owner'     => $subscription['Owner'],
                'protocol'  => $subscription['Protocol'],
                'endpoint'  => $subscription['Endpoint'],
                'topic_arn' => $subscription['TopicArn'],
            ];
        }
    }

    /**
     * @param Result $result
     */
    public function processSubscriptionAttributes(Result $result): void
    {
        $attributes = $result->get('Attributes');

        $this->data[self::SUBSCRIPTIONS][$attributes['SubscriptionArn']]['raw_message_delivery']      = $attributes['RawMessageDelivery'];
        $this->data[self::SUBSCRIPTIONS][$attributes['SubscriptionArn']]['effective_delivery_policy'] = $attributes['EffectiveDeliveryPolicy'];
        $this->data[self::SUBSCRIPTIONS][$attributes['SubscriptionArn']]['confirmation']              = [
            'pending'           => $attributes['PendingConfirmation'],
            'was_authenticated' => $attributes['ConfirmationWasAuthenticated'],
        ];
    }

    /**
     * @param Result $result
     */
    public function processTopics(Result $result): void
    {
        foreach ($result->get('Topics') as $topic) {
            $this->data[self::TOPICS][$topic['TopicArn']] = [];
        }
    }

    /**
     * @param Result $result
     */
    public function processTopicAttributes(Result $result): void
    {
        $attributes = $result->get('Attributes');

        $this->data[self::TOPICS][$attributes['TopicArn']] = [
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
     */
    public function getProcessedData(): array
    {
        $data       = $this->data;
        $this->data = [];

        return $data;
    }
}
