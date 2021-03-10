<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Manager;

use Aws\MockHandler;
use Aws\Result;
use Aws\Sns\SnsClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SnsManager;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * {@inheritdoc}
 */
final class SnsManagerTest extends TestCase
{
    /**
     * @var array<string, string>
     */
    private const TEST_ENDPOINT_CONFIG = [
        'scheme' => 'https',
        'host'   => 'serendipityhq.com',
    ];
    /**
     * @var array<string, string>
     */
    private const TEST = [
        'name' => 'topic-name',
        'arn'  => 'the-topic-arn',
    ];

    /** @var MockHandler $mockHandler */
    private $mockHandler;

    /** @var SnsClient $client */
    private $client;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->client      = new SnsClient([
            'region'      => 'eu-west-1',
            'version'     => 'latest',
            'handler'     => $this->mockHandler,
            'credentials' => [
                'key'    => 'key',
                'secret' => 'secret',
            ],
        ]);
    }

    public function testGetClient(): void
    {
        $mockClient  = $this->createMock(SnsClient::class);

        new SnsManager(self::TEST_ENDPOINT_CONFIG, $mockClient, $this->createMockRouter());
    }

    public function testCreateTopic(): void
    {
        $mockResult = new Result(['TopicArn' => self::TEST['arn']]);
        $this->mockHandler->append($mockResult);
        $resource = new SnsManager(self::TEST_ENDPOINT_CONFIG, $this->client, $this->createMockRouter());
        $result   = $resource->createTopic(self::TEST['name']);
        self::assertInstanceOf(Topic::class, $result);
        self::assertEquals(self::TEST['name'], $result->getName());
        self::assertEquals(self::TEST['arn'], $result->getArn());
    }

    /**
     * @return MockObject|RouterInterface
     */
    private function createMockRouter()
    {
        $mockContext = $this->createMock(RequestContext::class);
        $mockRouter  = $this->createMock(RouterInterface::class);

        $mockContext->expects(self::never())->method('setHost')->with(self::TEST_ENDPOINT_CONFIG['host']);
        $mockContext->expects(self::never())->method('setScheme')->with(self::TEST_ENDPOINT_CONFIG['scheme']);
        $mockRouter->expects(self::never())->method('getContext')->willReturn($mockContext);

        return $mockRouter;
    }
}
