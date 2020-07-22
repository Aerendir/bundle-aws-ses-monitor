<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use Aws\MockHandler;
use Aws\Result;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\AwsDataProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\IdentitiesStore;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\Monitor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\Console;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\IdentityGuesser;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class MonitorTest extends TestCase
{
    /** @var string $env */
    private $mockEnv = 'prod';

    /** @var IdentitiesStore|MockObject $configuredIdentities */
    private $mockConfiguredIdentities;

    /** @var array $liveData */
    private $mockLiveData;

    /** @var AwsDataProcessor|MockObject $awsDataProcessor */
    private $mockAwsDataProcessor;

    /** @var Console|MockObject $console */
    private $mockConsole;

    /** @var SesClient $sesClient */
    private $mockSesClient;

    /** @var MockHandler $mockSesClientHandler */
    private $mockSesClientHandler;

    /** @var SnsClient $snsClient */
    private $mockSnsClient;

    /** @var MockHandler $mockSnsClientHandler */
    private $mockSnsClientHandler;

    /** @var IdentityGuesser|MockObject $identityGuesser */
    private $mockIdentityGuesser;

    /** @var MockObject|OutputInterface $sectionTitle */
    private $mockSectionTitle;

    /** @var MockObject|OutputInterface $sectionBody */
    private $mockSectionBody;

    /** @var Monitor $resource */
    private $resource;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->mockConfiguredIdentities = $this->createMock(IdentitiesStore::class);
        $this->mockAwsDataProcessor     = $this->createMock(AwsDataProcessor::class);
        $this->mockConsole              = $this->createMock(Console::class);
        $this->mockIdentityGuesser      = $this->createMock(IdentityGuesser::class);
        $this->mockSesClientHandler     = new MockHandler();
        $this->mockSesClient            = new SesClient([
            'region'      => 'eu-west-1',
            'version'     => 'latest',
            'handler'     => $this->mockSesClientHandler,
            'credentials' => [
                'key'    => 'key',
                'secret' => 'secret',
            ],
        ]);
        $this->mockSnsClientHandler = new MockHandler();
        $this->mockSnsClient        = new SnsClient([
            'region'      => 'eu-west-1',
            'version'     => 'latest',
            'handler'     => $this->mockSnsClientHandler,
            'credentials' => [
                'key'    => 'key',
                'secret' => 'secret',
            ],
        ]);

        $this->resource = new Monitor(
            $this->mockEnv,
            $this->mockAwsDataProcessor,
            $this->mockConsole,
            $this->mockConfiguredIdentities,
            $this->mockIdentityGuesser,
            $this->mockSesClient,
            $this->mockSnsClient
        );

        // We mock OutputInterface to make tests pass with SF3.4
        $this->mockSectionTitle = $this->createMock(OutputInterface::class);
        $this->mockSectionBody  = $this->createMock(OutputInterface::class);
    }

    public function testRetrieveWithoutAccount()
    {
        $this->configureSesClient();
        $this->configureSnsClient();
        // Account data will not be fetched
        $this->mockAwsDataProcessor->expects(self::never())->method('processAccountSendingEnabled');
        $this->mockAwsDataProcessor->expects(self::never())->method('processAccountSendQuota');
        $this->mockAwsDataProcessor->expects(self::never())->method('processAccountSendStatistics');

        // Identities data will be fetched
        $this->mockAwsDataProcessor->expects(self::once())->method('processIdentitiesDkimAttributes');

        $this->resource->retrieve($this->mockSectionTitle, $this->mockSectionBody, false);
    }

    public function testRetrieveWithAccount()
    {
        $this->configureSesClient(true);
        $this->configureSnsClient();

        // Account data will not be fetched
        $this->mockAwsDataProcessor->expects(self::once())->method('processAccountSendingEnabled');
        $this->mockAwsDataProcessor->expects(self::once())->method('processAccountSendQuota');
        $this->mockAwsDataProcessor->expects(self::once())->method('processAccountSendStatistics');

        // Identities data will be fetched
        $this->mockAwsDataProcessor->expects(self::once())->method('processIdentitiesDkimAttributes');
        $this->mockAwsDataProcessor->expects(self::once())->method('processIdentitiesMailFromDomainAttributes');
        $this->mockAwsDataProcessor->expects(self::once())->method('processIdentitiesNotificationAttributes');
        $this->mockAwsDataProcessor->expects(self::once())->method('processIdentitiesVerificationAttributes');

        $this->resource->retrieve($this->mockSectionTitle, $this->mockSectionBody, true);
    }

    public function testGetAccount()
    {
        $test = [
            'enabled' => true,
            'quota'   => ['max_24_hour_send' => '', 'max_send_rate' => '', 'sent_last_24_hours' => ''],
        ];
        $this->mockAwsDataProcessor->expects(self::once())->method('getProcessedData')->willReturn([AwsDataProcessor::ACCOUNT => $test]);

        $this->callRetrieve();
        $result = $this->resource->getAccount();

        self::assertEquals($test, $result);
    }

    public function testGetAccountWithAttribute()
    {
        $test = [
            'enabled' => true,
            'quota'   => ['max_24_hour_send' => '', 'max_send_rate' => '', 'sent_last_24_hours' => ''],
        ];
        $this->mockAwsDataProcessor->expects(self::once())->method('getProcessedData')->willReturn([AwsDataProcessor::ACCOUNT => $test]);

        $this->callRetrieve();
        $result = $this->resource->getAccount('quota');

        self::assertEquals($test['quota'], $result);
    }

    public function testGetConfiguredIdentity()
    {
        $test = [
            'hello@serendipityhq.com' => [],
            'serendipityhq.com'       => ['called!'],
        ];
        $this->mockConfiguredIdentities->expects(self::once())->method('getIdentity')->with('serendipityhq.com')->willReturn($test['serendipityhq.com']);

        $result = $this->resource->getConfiguredIdentity('serendipityhq.com');

        self::assertEquals($test['serendipityhq.com'], $result);
    }

    /**
     * Tests finding an Identity that:.
     *
     * 1. Is an Email
     * 2. Is not explicitly configured
     * 3. Its domain identity is configured
     */
    public function testFindConfiguredIdentityEmailNotConfiguredButItsDomainIs()
    {
        $test = [
            'serendipityhq.com' => ['called!'],
        ];

        // 1. Is an Email
        $this->mockIdentityGuesser->expects(self::at(0))->method('isEmailIdentity')->with('hello@serendipityhq.com')->willReturn(true);

        // 2. Is not explicitly configured
        $this->mockConfiguredIdentities->expects(self::at(0))->method('identityExists')->with('hello@serendipityhq.com')->willReturn(false);

        $this->mockIdentityGuesser->expects(self::once())->method('getEmailParts')->with('hello@serendipityhq.com')->willReturn(['domain' => 'serendipityhq.com']);

        // 3. Its Domain identity is configured
        $this->mockConfiguredIdentities->expects(self::at(1))->method('identityExists')->with('serendipityhq.com')->willReturn(true);

        $this->mockConfiguredIdentities->expects(self::once())->method('getIdentity')->with('serendipityhq.com')->willReturn($test['serendipityhq.com']);
        $result = $this->resource->findConfiguredIdentity('hello@serendipityhq.com');

        self::assertEquals($test['serendipityhq.com'], $result);
    }

    /**
     * Tests finding an Identity that:.
     *
     * 1. Is an Email
     * 2. Is not explicitly configured
     * 3. Its domain identity is NOT configured
     */
    public function testFindConfiguredIdentityEmailNotConfiguredAndItsDomainIsntTooThrowsException()
    {
        // 1. Is an Email
        $this->mockIdentityGuesser->expects(self::exactly(2))->method('isEmailIdentity')->with('hello@serendipityhq.com')->willReturn(true);

        // 2. Is not explicitly configured
        $this->mockConfiguredIdentities->expects(self::at(0))->method('identityExists')->with('hello@serendipityhq.com')->willReturn(false);

        $this->mockIdentityGuesser->expects(self::once())->method('getEmailParts')->with('hello@serendipityhq.com')->willReturn(['domain' => 'serendipityhq.com']);

        // 3. Its Domain identity is NOT configured
        $this->mockConfiguredIdentities->expects(self::at(1))->method('identityExists')->with('serendipityhq.com')->willReturn(false);

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('The Email Identity "hello@serendipityhq.com" nor its Domain identity are configured.');
        $this->resource->findConfiguredIdentity('hello@serendipityhq.com');
    }

    /**
     * Tests finding an Identity that:.
     *
     * 1. Is an Email
     * 2. Is not explicitly configured
     * 3. Its domain identity is NOT configured
     */
    public function testFindConfiguredIdentityDomainNotConfiguredThrowsException()
    {
        // 1. Is an Email
        $this->mockIdentityGuesser->expects(self::exactly(2))->method('isEmailIdentity')->with('serendipityhq.com')->willReturn(false);

        // 2. Is not explicitly configured
        $this->mockConfiguredIdentities->expects(self::at(0))->method('identityExists')->with('serendipityhq.com')->willReturn(false);

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('The Domain Identity "serendipityhq.com" is not configured.');
        $this->resource->findConfiguredIdentity('serendipityhq.com');
    }

    public function testGetIdentityGuesser()
    {
        self::assertSame($this->mockIdentityGuesser, $this->resource->getIdentityGuesser());
    }

    private function callRetrieve()
    {
        $this->configureSesClient();
        $this->configureSnsClient();

        $this->resource->retrieve($this->mockSectionTitle, $this->mockSectionBody);
    }

    /**
     * @param bool $withAccount
     */
    private function configureSesClient($withAccount = false)
    {
        if ($withAccount) {
            $mockGetAccountSendingEnabled = new Result(['Enabled' => true]);
            $mockGetSendQuota             = new Result(['Max24HourSend' => '', 'MaxSendRate' => '', 'SentLast24Hours' => '']);
            $mockGetSendStatistics        = new Result(['SendDataPoints' => []]);

            $this->mockSesClientHandler->append($mockGetAccountSendingEnabled);
            $this->mockSesClientHandler->append($mockGetSendQuota);
            $this->mockSesClientHandler->append($mockGetSendStatistics);
        }

        $mockGetIdentityDkimAttributes           = new Result(['DkimAttributes' => []]);
        $mockGetIdentityMailFromDomainAttributes = new Result(['MailFromDomainAttributes' => []]);
        $mockGetIdentityNotificationAttributes   = new Result(['NotificationAttributes' => []]);
        $mockGetIdentityVerificationAttributes   = new Result(['VerificationAttributes' => []]);

        $this->mockSesClientHandler->append($mockGetIdentityDkimAttributes);
        $this->mockSesClientHandler->append($mockGetIdentityMailFromDomainAttributes);
        $this->mockSesClientHandler->append($mockGetIdentityNotificationAttributes);
        $this->mockSesClientHandler->append($mockGetIdentityVerificationAttributes);
    }

    private function configureSnsClient()
    {
        $mockListTopics = new Result(['Topics' => [
            [
                'TopicArn' => 'dummy:topic:arn',
            ],
        ]]);
        $mockGetTopicAttributes = new Result(['Attributes', [
            [
                'TopicArn'                => 'topic:arn:12345',
                'DisplayName'             => '',
                'Policy'                  => '{}',
                'Owner'                   => '1234567890',
                'EffectiveDeliveryPolicy' => '{}',
                'SubscriptionsConfirmed'  => 0,
                'SubscriptionsDeleted'    => 0,
                'SubscriptionsPending'    => 1,
            ],
        ]]);
        $mockListSubscriptions = new Result(['Subscriptions' => [
            [
                'SubscriptionArn' => 'subscription:arn:12345',
                'Owner'           => 1234567890,
                'Protocol'        => 'https',
                'Endpoint'        => 'endpoint',
                'TopicArn'        => 'topic:arn:12345',
            ],
            [
                'SubscriptionArn' => 'PendingConfirmation',
            ],
        ]]);
        $mockGetSubscriptionAttributes = new Result(['Attributes' => [
            'SubscriptionArn'              => 'subscription:arn:12345',
            'RawMessageDelivery'           => false,
            'EffectiveDeliveryPolicy'      => '{}',
            'PendingConfirmation'          => false,
            'ConfirmationWasAuthenticated' => true,
        ]]);

        $this->mockSnsClientHandler->append($mockListTopics);
        $this->mockSnsClientHandler->append($mockGetTopicAttributes);
        $this->mockSnsClientHandler->append($mockListSubscriptions);
        $this->mockSnsClientHandler->append($mockGetSubscriptionAttributes);
    }
}
