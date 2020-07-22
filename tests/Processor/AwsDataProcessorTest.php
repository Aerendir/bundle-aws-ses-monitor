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
use PHPUnit\Framework\TestCase;

/**
 * {@inheritdoc}
 */
class AwsDataProcessorTest extends TestCase
{
    public function testProcessAccountSendingEnabled()
    {
        $testValue  = true;
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('Enabled')->willReturn($testValue);

        $resource = new AwsDataProcessor();
        $resource->processAccountSendingEnabled($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($testValue, $result[AwsDataProcessor::ACCOUNT]['enabled']);
    }

    public function testProcessAccountSendQuota()
    {
        $test = [
            'Max24HourSend'   => 1,
            'MaxSendRate'     => 1,
            'SentLast24Hours' => 1,
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::exactly(3))->method('get')->willReturnMap([
            ['Max24HourSend', $test['Max24HourSend']],
            ['MaxSendRate', $test['MaxSendRate']],
        ['SentLast24Hours', $test['SentLast24Hours']],
        ]);

        $resource = new AwsDataProcessor();
        $resource->processAccountSendQuota($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['Max24HourSend'], $result[AwsDataProcessor::ACCOUNT]['quota']['max_24_hour_send']);
        self::assertEquals($test['MaxSendRate'], $result[AwsDataProcessor::ACCOUNT]['quota']['max_send_rate']);
        self::assertEquals($test['SentLast24Hours'], $result[AwsDataProcessor::ACCOUNT]['quota']['sent_last_24_hours']);
    }

    public function testProcessAccountSendStatistics()
    {
        $test = [
            [
                'Bounces'          => 0,
                'Complaints'       => 0,
                'DeliveryAttempts' => 1,
                'Rejects'          => 0,
                'Timestamp'        => 'the-time-stamp',
            ],
            [
                'Bounces'          => 3,
                'Complaints'       => 2,
                'DeliveryAttempts' => 6,
                'Rejects'          => 2,
                'Timestamp'        => 'the-time-stamp',
            ],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('SendDataPoints')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processAccountSendStatistics($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test, $result[AwsDataProcessor::ACCOUNT]['stats']);
    }

    public function testProcessIdentitiesDkimAttributes()
    {
        $test = [
            'serendipityhq.com' => [
                'DkimEnabled'            => true,
                'DkimVerificationStatus' => 'Pending',
                'DkimTokens'             => ['token1', 'token2', 'token3'],
            ],
            'hello@serendipityhq.com' => [
                'DkimEnabled'            => false,
                'DkimVerificationStatus' => 'NotStarted',
            ],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('DkimAttributes')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processIdentitiesDkimAttributes($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['serendipityhq.com']['DkimEnabled'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['dkim']['enabled']);
        self::assertEquals($test['serendipityhq.com']['DkimVerificationStatus'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['dkim']['verification_status']);
        self::assertEquals($test['serendipityhq.com']['DkimTokens'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['dkim']['tokens']);

        self::assertEquals($test['hello@serendipityhq.com']['DkimEnabled'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['dkim']['enabled']);
        self::assertEquals($test['hello@serendipityhq.com']['DkimVerificationStatus'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['dkim']['verification_status']);
        self::assertArrayNotHasKey('tokens', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['dkim']);
    }

    public function testProcessIdentitiesMailFromDomainAttributes()
    {
        $test = [
            'serendipityhq.com' => [
                'BehaviorOnMXFailure'  => 'Reject',
                'MailFromDomainStatus' => 'Verified',
                'MailFromDomain'       => 'www.serendipityhq.com',
            ],
            'hello@serendipityhq.com' => [
                'BehaviorOnMXFailure' => 'DefaultValue',
            ],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('MailFromDomainAttributes')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processIdentitiesMailFromDomainAttributes($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['serendipityhq.com']['BehaviorOnMXFailure'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['mail_from']['on_mx_failure']);
        self::assertEquals($test['serendipityhq.com']['MailFromDomainStatus'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['mail_from']['status']);
        self::assertEquals($test['serendipityhq.com']['MailFromDomain'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['mail_from']['domain']);

        self::assertEquals($test['hello@serendipityhq.com']['BehaviorOnMXFailure'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['mail_from']['on_mx_failure']);
        self::assertArrayNotHasKey('status', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['mail_from']);
        self::assertArrayNotHasKey('domain', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['mail_from']);
    }

    public function testProcessIdentitiesNotificationAttributes()
    {
        $test = [
            'serendipityhq.com' => [
                'ForwardingEnabled'                      => true,
                'HeadersInBounceNotificationsEnabled'    => false,
                'HeadersInComplaintNotificationsEnabled' => false,
                'HeadersInDeliveryNotificationsEnabled'  => false,
                'BounceTopic'                            => 'topic:arn:bounces',
                'ComplaintTopic'                         => 'topic:arn:complaint',
                'DeliveryTopic'                          => 'topic:arn:delivery',
            ],
            'hello@serendipityhq.com' => [
                'ForwardingEnabled'                      => false,
                'HeadersInBounceNotificationsEnabled'    => true,
                'HeadersInComplaintNotificationsEnabled' => true,
                'HeadersInDeliveryNotificationsEnabled'  => true,
            ],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('NotificationAttributes')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processIdentitiesNotificationAttributes($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['serendipityhq.com']['ForwardingEnabled'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['forwarding_enabled']);
        self::assertEquals($test['serendipityhq.com']['HeadersInBounceNotificationsEnabled'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['bounces']['include_headers']);
        self::assertEquals($test['serendipityhq.com']['HeadersInComplaintNotificationsEnabled'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['complaints']['include_headers']);
        self::assertEquals($test['serendipityhq.com']['HeadersInDeliveryNotificationsEnabled'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['deliveries']['include_headers']);
        self::assertEquals($test['serendipityhq.com']['BounceTopic'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['bounces']['topic']);
        self::assertEquals($test['serendipityhq.com']['ComplaintTopic'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['complaints']['topic']);
        self::assertEquals($test['serendipityhq.com']['DeliveryTopic'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['notifications']['deliveries']['topic']);

        self::assertEquals($test['hello@serendipityhq.com']['ForwardingEnabled'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['forwarding_enabled']);
        self::assertEquals($test['hello@serendipityhq.com']['HeadersInBounceNotificationsEnabled'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['bounces']['include_headers']);
        self::assertEquals($test['hello@serendipityhq.com']['HeadersInComplaintNotificationsEnabled'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['complaints']['include_headers']);
        self::assertEquals($test['hello@serendipityhq.com']['HeadersInDeliveryNotificationsEnabled'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['deliveries']['include_headers']);
        self::assertArrayNotHasKey('topic', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['bounces']);
        self::assertArrayNotHasKey('topic', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['complaints']);
        self::assertArrayNotHasKey('topic', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['notifications']['deliveries']);
    }

    public function testProcessIdentitiesVerificationAttributes()
    {
        $test = [
            'serendipityhq.com' => [
                'VerificationStatus' => 'Pending',
                'VerificationToken'  => 'token',
            ],
            'hello@serendipityhq.com' => [
                'VerificationStatus' => 'Pending',
            ],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('VerificationAttributes')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processIdentitiesVerificationAttributes($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['serendipityhq.com']['VerificationStatus'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['verification']['status']);
        self::assertEquals($test['serendipityhq.com']['VerificationToken'], $result[AwsDataProcessor::IDENTITIES]['serendipityhq.com']['verification']['token']);

        self::assertEquals($test['hello@serendipityhq.com']['VerificationStatus'], $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['verification']['status']);
        self::assertArrayNotHasKey('token', $result[AwsDataProcessor::IDENTITIES]['hello@serendipityhq.com']['verification']);
    }

    public function testProcessSubscriptions()
    {
        $test = [
            'serendipityhq.com' => [
                'SubscriptionArn' => 'subscription:arn:12345',
                'Owner'           => 1234567890,
                'Protocol'        => 'https',
                'Endpoint'        => 'endpoint',
                'TopicArn'        => 'topic:arn:12345',
            ],
            'hello@serendipityhq.com' => [
                'SubscriptionArn' => 'PendingConfirmation',
            ],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('Subscriptions')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processSubscriptions($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['serendipityhq.com']['SubscriptionArn'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['serendipityhq.com']['SubscriptionArn']]['subscription_arn']);
        self::assertEquals($test['serendipityhq.com']['Owner'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['serendipityhq.com']['SubscriptionArn']]['owner']);
        self::assertEquals($test['serendipityhq.com']['Protocol'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['serendipityhq.com']['SubscriptionArn']]['protocol']);
        self::assertEquals($test['serendipityhq.com']['Endpoint'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['serendipityhq.com']['SubscriptionArn']]['endpoint']);
        self::assertEquals($test['serendipityhq.com']['TopicArn'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['serendipityhq.com']['SubscriptionArn']]['topic_arn']);

        self::assertArrayNotHasKey('PendingConfirmation', $result[AwsDataProcessor::SUBSCRIPTIONS]);
    }

    public function testProcessSubscriptionAttributes()
    {
        $test = [
            'SubscriptionArn'              => 'subscription:arn:12345',
            'RawMessageDelivery'           => false,
            'EffectiveDeliveryPolicy'      => '{}',
            'PendingConfirmation'          => false,
            'ConfirmationWasAuthenticated' => true,
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('Attributes')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processSubscriptionAttributes($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['RawMessageDelivery'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['SubscriptionArn']]['raw_message_delivery']);
        self::assertEquals($test['EffectiveDeliveryPolicy'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['SubscriptionArn']]['effective_delivery_policy']);
        self::assertEquals($test['PendingConfirmation'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['SubscriptionArn']]['confirmation']['pending']);
        self::assertEquals($test['ConfirmationWasAuthenticated'], $result[AwsDataProcessor::SUBSCRIPTIONS][$test['SubscriptionArn']]['confirmation']['was_authenticated']);
    }

    public function testProcessTopics()
    {
        $test = [
            ['TopicArn' => 'topic:arn:12345'],
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('Topics')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processTopics($mockResult);

        $result = $resource->getProcessedData();

        self::assertEmpty($result[AwsDataProcessor::TOPICS][$test[0]['TopicArn']]);
    }

    public function testProcessTopicAttributes()
    {
        $test = [
            'TopicArn'                => 'topic:arn:12345',
            'DisplayName'             => '',
            'Policy'                  => '{}',
            'Owner'                   => '1234567890',
            'EffectiveDeliveryPolicy' => '{}',
            'SubscriptionsConfirmed'  => 0,
            'SubscriptionsDeleted'    => 0,
            'SubscriptionsPending'    => 1,
        ];
        $mockResult = $this->createMock(Result::class);
        $mockResult->expects(self::once())->method('get')->with('Attributes')->willReturn($test);

        $resource = new AwsDataProcessor();
        $resource->processTopicAttributes($mockResult);

        $result = $resource->getProcessedData();

        self::assertEquals($test['TopicArn'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['arn']);
        self::assertEquals($test['DisplayName'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['display_name']);
        self::assertEquals($test['Policy'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['policy']);
        self::assertEquals($test['Owner'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['owner']);
        self::assertEquals($test['EffectiveDeliveryPolicy'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['effective_delivery_policy']);
        self::assertEquals($test['SubscriptionsConfirmed'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['subscriptions']['confirmed']);
        self::assertEquals($test['SubscriptionsDeleted'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['subscriptions']['deleted']);
        self::assertEquals($test['SubscriptionsPending'], $result[AwsDataProcessor::TOPICS][$test['TopicArn']]['subscriptions']['pending']);
    }
}
