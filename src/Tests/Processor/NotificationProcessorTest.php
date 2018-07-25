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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\BounceNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\ComplaintNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\DeliveryNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\NotificationProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class NotificationProcessorTest extends TestCase
{
    /** @var NotificationProcessor $notificationProcessor */
    private $notificationProcessor;

    /** @var MockObject $bounceNotificationHandler */
    private $bounceNotificationHandler;

    /** @var MockObject $complaintNotificationHandler */
    private $complaintNotificationHandler;

    /** @var MockObject $deliveryNotificationHandler */
    private $deliveryNotificationHandler;

    /** @var MockObject $mockEntityManager */
    private $mockEntityManager;

    /** @var MockObject $mockMessageHelper */
    private $mockMessageHelper;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->bounceNotificationHandler   = $this->createMock(BounceNotificationHandler::class);
        $this->complaintNotificationHandler= $this->createMock(ComplaintNotificationHandler::class);
        $this->deliveryNotificationHandler = $this->createMock(DeliveryNotificationHandler::class);
        $this->mockEntityManager           = $this->createMock(EntityManagerInterface::class);
        $this->mockMessageHelper           = $this->createMock(MessageHelper::class);

        $this->notificationProcessor = new NotificationProcessor(
            $this->bounceNotificationHandler,
            $this->complaintNotificationHandler,
            $this->deliveryNotificationHandler,
            $this->mockEntityManager,
            $this->mockMessageHelper
        );
    }

    public function testMessageMustBeValid()
    {
        $mockRequest    = $this->createMock(Request::class);
        $mockNsnMessage = $this->createMock(Message::class);
        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(false);

        $response = $this->notificationProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('The message is invalid.', $response->getContent());
        self::assertEquals(403, $response->getStatusCode());
    }

    public function testNotificationTypeMustExist()
    {
        $mockRequest    = $this->createMock(Request::class);
        $mockNsnMessage = $this->createMock(Message::class);
        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(true);
        $this->mockMessageHelper->expects(self::exactly(1))->method('extractMessageData')->willReturn([]);

        $response = $this->notificationProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('Missed NotificationType.', $response->getContent());
        self::assertEquals(403, $response->getStatusCode());
    }

    public function testSubscriptionSuccessImmediatelyReturns()
    {
        $mockRequest    = $this->createMock(Request::class);
        $mockNsnMessage = $this->createMock(Message::class);
        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(true);
        $this->mockMessageHelper->expects(self::exactly(1))->method('extractMessageData')->willReturn([
            'notificationType' => SnsTypes::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS,
        ]);

        $response = $this->notificationProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('OK', $response->getContent());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testBounceHandling()
    {
        $mockResponse = $this->initializeSwitch(SnsTypes::MESSAGE_TYPE_BOUNCE);
        $mockRequest  = $this->createMock(Request::class);

        $response = $this->notificationProcessor->processRequest($mockRequest);
        self::assertSame($mockResponse, $response);
    }

    public function testComplaintHandling()
    {
        $mockResponse = $this->initializeSwitch(SnsTypes::MESSAGE_TYPE_COMPLAINT);
        $mockRequest  = $this->createMock(Request::class);

        $response = $this->notificationProcessor->processRequest($mockRequest);
        self::assertSame($mockResponse, $response);
    }

    public function testDeliveryHandling()
    {
        $mockResponse = $this->initializeSwitch(SnsTypes::MESSAGE_TYPE_DELIVERY);
        $mockRequest  = $this->createMock(Request::class);

        $response = $this->notificationProcessor->processRequest($mockRequest);
        self::assertSame($mockResponse, $response);
    }

    public function testUnknownMessageTypeReturnsErrorResponse()
    {
        $this->initializeSwitch('dummy_message');
        $mockRequest = $this->createMock(Request::class);

        $response = $this->notificationProcessor->processRequest($mockRequest);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('Notification type not understood', $response->getContent());
        self::assertEquals(403, $response->getStatusCode());
    }

    /**
     * @param string $messageType
     *
     * @return MockObject|Response
     */
    private function initializeSwitch(string $messageType)
    {
        $test = [
            'notificationType' => $messageType,
            'mail'             => [
                'messageId'        => 'test-message-id',
                'timestamp'        => (new \DateTime())->format('Y-m-d H:i:s'),
                'source'           => 'test@example.com',
                'sourceArn'        => 'test-source-arn',
                'sendingAccountId' => 'test-sending-account-id',
                'headers'          => 'test-headers',
                'commonHeaders'    => 'test-common-headers',
            ],
        ];

        $mockNsnMessage = $this->createMock(Message::class);
        $this->mockMessageHelper->expects(self::exactly(1))->method('buildMessageFromRequest')->willReturn($mockNsnMessage);
        $this->mockMessageHelper->expects(self::exactly(1))->method('validateNotification')->willReturn(true);
        $this->mockMessageHelper->expects(self::exactly(1))->method('extractMessageData')->willReturn($test);

        $this->configureLoadOrCreateMailMessage($test['mail']['messageId']);

        $mockResponse = $this->createMock(Response::class);

        switch ($messageType) {
            case SnsTypes::MESSAGE_TYPE_BOUNCE:
                $this->bounceNotificationHandler->expects(self::exactly(1))->method('processNotification')->willReturn($mockResponse);
                break;
            case SnsTypes::MESSAGE_TYPE_COMPLAINT:
                $this->complaintNotificationHandler->expects(self::exactly(1))->method('processNotification')->willReturn($mockResponse);
                break;
            case SnsTypes::MESSAGE_TYPE_DELIVERY:
                $this->deliveryNotificationHandler->expects(self::exactly(1))->method('processNotification')->willReturn($mockResponse);
                break;
        }

        return $mockResponse;
    }

    /**
     * @param string $messageId
     */
    private function configureLoadOrCreateMailMessage(string $messageId)
    {
        $mockMailMessageRepository = $this->createMock(EntityRepository::class);
        $mockMailMessageRepository
            ->expects(self::exactly(1))
            ->method('findOneBy')
            ->with(['messageId' => $messageId])
            ->willReturn(null);
        $this->mockEntityManager->expects(self::exactly(1))->method('getRepository')->willReturn($mockMailMessageRepository);
        $this->mockEntityManager->expects(self::exactly(1))->method('persist');
    }
}
