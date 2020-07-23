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
class SnsManagerTest extends TestCase
{
    private $testEndpointConfig = [
        'scheme' => 'https',
        'host'   => 'serendipityhq.com',
    ];

    /** @var MockHandler $mockHandler */
    private $mockHandler;

    /** @var SnsClient $client */
    private $client;

    public function setUp(): void
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

    public function testGetClient()
    {
        $mockClient  = $this->createMock(SnsClient::class);

        new SnsManager($this->testEndpointConfig, $mockClient, $this->createMockRouter());
    }

    public function testCreateTopic()
    {
        $test = [
            'name' => 'topic-name',
            'arn'  => 'the-topic-arn',
        ];

        $mockResult = new Result(['TopicArn' => $test['arn']]);
        $this->mockHandler->append($mockResult);

        $resource = new SnsManager($this->testEndpointConfig, $this->client, $this->createMockRouter());
        $result   = $resource->createTopic($test['name']);

        self::assertInstanceOf(Topic::class, $result);
        self::assertEquals($test['name'], $result->getName());
        self::assertEquals($test['arn'], $result->getArn());
    }

    /**
     * @return MockObject|RouterInterface
     */
    private function createMockRouter()
    {
        $mockContext = $this->createMock(RequestContext::class);
        $mockRouter  = $this->createMock(RouterInterface::class);

        $mockContext->expects(self::never())->method('setHost')->with($this->testEndpointConfig['host']);
        $mockContext->expects(self::never())->method('setScheme')->with($this->testEndpointConfig['scheme']);
        $mockRouter->expects(self::never())->method('getContext')->willReturn($mockContext);

        return $mockRouter;
    }
}
