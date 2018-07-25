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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Processor;

use Aws\Sns\Message;
use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\SubscriptionProcessor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class SubscriptionProcessorTest extends TestCase
{
    /** @var SubscriptionProcessor $requestProcessor */
    private $subscriptionProcessor;

    /** @var MockObject $mockSnsClient */
    private $mockSnsClient;

    /** @var MockObject $mockEntityManager */
    private $mockEntityManager;

    /** @var MockObject $mockMessageHelper */
    private $mockMessageHelper;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockSnsClient     = $this->createMock(SnsClient::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockMessageHelper = $this->createMock(MessageHelper::class);

        $this->subscriptionProcessor = new SubscriptionProcessor(
            $this->mockSnsClient, $this->mockEntityManager, $this->mockMessageHelper
        );
    }

    public function testMessageMustBeValid()
    {
        $mockRequest    = $this->createMock(Request::class);
        $mockNsnMessage = $this->createMock(Message::class);
        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(false);

        $response = $this->subscriptionProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('The message is invalid.', $response->getContent());
        self::assertEquals(403, $response->getStatusCode());
    }

    public function testTopicMustExist()
    {
        $testMessage    = ['TopicArn' => 'dummy:topic:arn', 'Token' => 'dUMmyT0k3N'];
        $mockRequest    = $this->createMock(Request::class);
        $mockNsnMessage = $this->createMock(Message::class);
        $mockNsnMessage->expects(self::exactly(1))->method('offsetGet')->with('TopicArn')->willReturn($testMessage['TopicArn']);

        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(true);

        $mockTopicRepository = $this->createMock(EntityRepository::class);
        $mockTopicRepository
            ->expects(self::exactly(1))
            ->method('findOneBy')
            ->with(['arn' => $testMessage['TopicArn']])
            ->willReturn(null);
        $this->mockEntityManager->expects(self::exactly(1))->method('getRepository')->willReturn($mockTopicRepository);

        $response = $this->subscriptionProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('Topic not found', $response->getContent());
        self::assertEquals(404, $response->getStatusCode());
    }

    public function testOkResponse()
    {
        $testMessage    = ['TopicArn' => 'dummy:topic:arn', 'Token' => 'dUMmyT0k3N'];
        $mockRequest    = $this->createMock(Request::class);
        $mockNsnMessage = $this->createMock(Message::class);
        $mockNsnMessage
            ->expects(self::exactly(2))
            ->method('offsetGet')
            ->with(self::logicalOr(
                self::equalTo('TopicArn'),
                self::equalTo('Token')
            ))
            ->will(self::returnCallback(function ($key) use ($testMessage) { return $testMessage[$key]; }));

        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(true);

        $mockTopic = $this->createMock(Topic::class);
        $mockTopic->expects(self::exactly(1))->method('getArn')->willReturn($testMessage['TopicArn']);

        $mockTopicRepository = $this->createMock(EntityRepository::class);
        $mockTopicRepository
            ->expects(self::exactly(1))
            ->method('findOneBy')
            ->with(['arn' => $testMessage['TopicArn']])
            ->willReturn($mockTopic);

        $this->mockEntityManager->expects(self::exactly(1))->method('getRepository')->willReturn($mockTopicRepository);
        $this->mockEntityManager->expects(self::exactly(1))->method('flush');

        $response = $this->subscriptionProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('OK', $response->getContent());
        self::assertEquals(200, $response->getStatusCode());
    }
}
