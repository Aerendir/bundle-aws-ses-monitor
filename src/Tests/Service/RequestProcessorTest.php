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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\RequestProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\SubscriptionProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * {@inheritdoc}
 */
class RequestProcessorTest extends TestCase
{
    /** @var RequestProcessor $requestProcessor */
    private $requestProcessor;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $mockResponse = $this->createMock(Response::class);

        $mockNotificationProcessor = $this->createMock(NotificationProcessor::class);
        $mockNotificationProcessor->method('processRequest')->willReturn($mockResponse);

        $mockSubscriptionProcessor = $this->createMock(SubscriptionProcessor::class);
        $mockSubscriptionProcessor->method('processRequest')->willReturn($mockResponse);

        $this->requestProcessor = new RequestProcessor($mockNotificationProcessor, $mockSubscriptionProcessor);
    }

    public function testMethodMustBePost()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects(self::exactly(1))->method('isMethod')->willReturn(false);
        $response = $this->requestProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('Only POST requests are accepted.', $response->getContent());
        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSnsTypeHeaderMustBeSet()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects(self::exactly(1))->method('isMethod')->willReturn(true);

        $mockHeaders = $this->createMock(HeaderBag::class);
        $mockHeaders->expects(self::exactly(1))->method('get')->willReturn(null);

        $mockRequest->headers = $mockHeaders;

        $this->expectException(BadRequestHttpException::class);
        $this->requestProcessor->processRequest($mockRequest);
    }

    public function testUnknownSnsTypeHeaderThrowAnException()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects(self::exactly(1))->method('isMethod')->willReturn(true);

        $mockHeaders = $this->createMock(HeaderBag::class);
        $mockHeaders->expects(self::exactly(1))->method('get')->willReturn('dummy-header');

        $mockRequest->headers = $mockHeaders;

        $this->expectException(\RuntimeException::class);
        $this->requestProcessor->processRequest($mockRequest);
    }

    public function testNotificationProcessing()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects(self::exactly(1))->method('isMethod')->willReturn(true);

        $mockHeaders = $this->createMock(HeaderBag::class);
        $mockHeaders->expects(self::exactly(1))->method('get')->willReturn(SnsTypes::HEADER_TYPE_NOTIFICATION);

        $mockRequest->headers = $mockHeaders;

        $result = $this->requestProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $result);
    }

    public function testSubscriptionConfirmationProcessing()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects(self::exactly(1))->method('isMethod')->willReturn(true);

        $mockHeaders = $this->createMock(HeaderBag::class);
        $mockHeaders->expects(self::exactly(1))->method('get')->willReturn(SnsTypes::HEADER_TYPE_CONFIRM_SUBSCRIPTION);

        $mockRequest->headers = $mockHeaders;

        $result = $this->requestProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $result);
    }
}
