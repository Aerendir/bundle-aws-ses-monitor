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

use Aws\Credentials\Credentials;
use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\MessageValidator;
use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;
use SerendipityHQ\Library\PHPUnit_Helper\PHPUnitHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class NotificationHandlerTest extends TestCase
{
    use PHPUnitHelper;

    /** @var Request */
    private $mockRequest;

    /** @var Credentials */
    private $mockCredentials;

    /** @var EntityManager */
    private $mockEntityManager;

    /** @var MessageValidator */
    private $mockMessageValidator;

    /** @var SnsClient */
    private $mockSnsClient;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRequest          = $this->createMock(Request::class);
        $this->mockCredentials      = $this->createMock(Credentials::class);
        $this->mockEntityManager    = $this->createMock(EntityManager::class);
        $this->mockMessageValidator = $this->createMock(MessageValidator::class);
    }

    public function testReturn405IfMethodIsNotPost()
    {
        $this->mockRequest->method('isMethod')->willReturn(false);
        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(405, $response['code'], $response['content']);
    }

    public function testReturn403IfMessageIsNotValid()
    {
        $data = [
            'MailMessage'      => '',
            'MessageId'        => '',
            'Timestamp'        => '',
            'TopicArn'         => '',
            'Type'             => 'test',
            'Signature'        => '',
            'SigningCertURL'   => '',
            'SignatureVersion' => '',
        ];
        $encodedData = json_encode($data);

        $this->mockRequest->method('isMethod')->willReturn(true);
        $this->mockRequest->method('getContent')->willReturn($encodedData);
        $this->mockMessageValidator->method('isValid')->willReturn(false);

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(403, $response['code'], $response['content']);
    }

    public function testReturn403IfMessageValidationTrhowsAnException()
    {
        $data = [
            'MailMessage'      => '',
            'MessageId'        => '',
            'Timestamp'        => '',
            'TopicArn'         => '',
            'Type'             => 'test',
            'Signature'        => '',
            'SigningCertURL'   => '',
            'SignatureVersion' => '',
        ];
        $encodedData = json_encode($data);

        $this->mockRequest->method('isMethod')->willReturn(true);
        $this->mockRequest->method('getContent')->willReturn($encodedData);
        $this->mockMessageValidator->method('isValid')->willThrowException(new InvalidSnsMessageException('An error message'));

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(403, $response['code'], $response['content']);
    }

    public function testReturn403IfTokenOrTopicArnAreNotSet()
    {
        $data = [
            'MailMessage'      => '',
            'MessageId'        => '',
            'Timestamp'        => '',
            'TopicArn'         => '',
            'Type'             => 'test',
            'Signature'        => '',
            'SigningCertURL'   => '',
            'SignatureVersion' => '',
        ];
        $encodedData = json_encode($data);

        $this->mockRequest->method('isMethod')->willReturn(true);
        $this->mockRequest->method('getContent')->willReturn($encodedData);
        $this->mockMessageValidator->method('isValid')->willReturn(true);

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(403, $response['code'], $response['content']);
    }

    public function testReturn403IfMessageIsNotSet()
    {
        $data        = $this->configureAWorkingResponse();
        $encodedData = json_encode($data);
        $this->mockRequest->method('getContent')->willReturn($encodedData);

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(403, $response['code'], $response['content']);
    }

    public function testHandleMailMessageReturnsANewMailMessageObject()
    {
        $data = $this->configureAWorkingResponse();
        $this->mockRequest->method('getContent')->willReturn(json_encode($data));

        $this->mockNullMailMessage();

        $mail = $this->getMailArray();

        $handler = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        /** @var MailMessage $result */
        $result = $this->invokeMethod($handler, 'handleMailMessage', [$this->getMailArray()]);
        self::assertInstanceOf(MailMessage::class, $result);
        self::assertInstanceOf(\DateTime::class, $result->getSentOn());
        self::assertSame($mail['messageId'], $result->getMessageId());
        self::assertSame($mail['source'], $result->getSentFrom());
        self::assertSame($mail['sourceArn'], $result->getSourceArn());
        self::assertSame($mail['sendingAccountId'], $result->getSendingAccountId());
        self::assertSame($mail['headers'], $result->getHeaders());
        self::assertSame($mail['commonHeaders'], $result->getCommonHeaders());
    }

    public function testHandleMailMessageReturnsAnAlreadyExistentMailMessageObject()
    {
        $data = $this->configureAWorkingResponse();
        $this->mockRequest->method('getContent')->willReturn(json_encode($data));

        $mail = [
            'messageId' => 'AlreadyExistenMessageId',
        ];

        $mockMailMessage = $this->createMock(MailMessage::class);
        $mockMailMessage->method('getMessageId')->willReturn($mail['messageId']);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByMessageId')->willReturn($mockMailMessage);
        $this->mockEntityManager->method('getRepository')->willReturn($mockMailMessageRepository);

        $handler = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        /** @var MailMessage $result */
        $result = $this->invokeMethod($handler, 'handleMailMessage', [$mail]);
        self::assertInstanceOf(MailMessage::class, $result);
        self::assertSame($mail['messageId'], $result->getMessageId());
    }

    public function testHandleRequestSubscriptionSuccessPath()
    {
        $data = $this->configureAWorkingResponse();

        $message = [
            'mail'             => $this->getMailArray(),
            'notificationType' => NotificationHandler::MESSAGE_TYPE_SUBSCRIPTION_SUCCESS,
        ];

        $data['Message'] = json_encode($message);

        $data = json_encode($data);
        $this->mockRequest->method('getContent')->willReturn($data);

        $this->mockNullMailMessage();

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(200, $response['code'], $response['content']);
    }

    public function testHandleBounceNotification()
    {
        $message = $this->getBouncedNotificationMessage();

        $mockEmailStatus = $this->createMock(Email::class);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByEmailAddress')->willReturn($mockEmailStatus);
        $this->mockEntityManager->method('getRepository')->willReturn($mockMailMessageRepository);

        $mailMessage = $this->createMock(MailMessage::class);

        $handler = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        /** @var MailMessage $result */
        $result = $this->invokeMethod($handler, 'handleBounceNotification', [$message, $mailMessage]);
        self::assertSame(200, $result);
    }

    public function testHandleRequestBouncePath()
    {
        $data = $this->configureAWorkingResponse();

        $message = $this->getBouncedNotificationMessage();

        $data['Message'] = json_encode($message);

        $data = json_encode($data);
        $this->mockRequest->method('getContent')->willReturn($data);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByMessageId')->willReturn(null);

        $mockEmailStatusRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockEmailStatusRepository->method('__call')->with('findOneByEmailAddress')->willReturn(null);

        $this->mockEntityManager
            ->expects(self::exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($mockMailMessageRepository, $mockEmailStatusRepository);

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(200, $response['code'], $response['content']);
    }

    public function testHandleComplaintNotification()
    {
        $message = $this->getComplainedNotificationMessage();

        $mockEmailStatus = $this->createMock(Email::class);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByEmailAddress')->willReturn($mockEmailStatus);
        $this->mockEntityManager->method('getRepository')->willReturn($mockMailMessageRepository);

        $mailMessage = $this->createMock(MailMessage::class);

        $handler = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        /** @var MailMessage $result */
        $result = $this->invokeMethod($handler, 'handleComplaintNotification', [$message, $mailMessage]);
        self::assertSame(200, $result);
    }

    public function testHandleRequestComplaintPath()
    {
        $data = $this->configureAWorkingResponse();

        $message = $this->getComplainedNotificationMessage();

        $data['Message'] = json_encode($message);

        $data = json_encode($data);
        $this->mockRequest->method('getContent')->willReturn($data);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByMessageId')->willReturn(null);

        $mockEmailStatusRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockEmailStatusRepository->method('__call')->with('findOneByEmailAddress')->willReturn(null);

        $this->mockEntityManager
            ->expects(self::exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($mockMailMessageRepository, $mockEmailStatusRepository);

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(200, $response['code'], $response['content']);
    }

    public function testHandleDeliveryNotification()
    {
        $message = $this->getDeliveredNotificationMessage();

        $mockEmailStatus = $this->createMock(Email::class);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByEmailAddress')->willReturn($mockEmailStatus);
        $this->mockEntityManager->method('getRepository')->willReturn($mockMailMessageRepository);

        $mailMessage = $this->createMock(MailMessage::class);

        $handler = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        /** @var MailMessage $result */
        $result = $this->invokeMethod($handler, 'handleDeliveryNotification', [$message, $mailMessage]);
        self::assertSame(200, $result);
    }

    public function testHandleRequestDeliveryPath()
    {
        $data = $this->configureAWorkingResponse();

        $message = $this->getDeliveredNotificationMessage();

        $data['Message'] = json_encode($message);

        $data = json_encode($data);
        $this->mockRequest->method('getContent')->willReturn($data);

        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByMessageId')->willReturn(null);

        $mockEmailStatusRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockEmailStatusRepository->method('__call')->with('findOneByEmailAddress')->willReturn(null);

        $this->mockEntityManager
            ->expects(self::exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($mockMailMessageRepository, $mockEmailStatusRepository);

        $handler  = new NotificationHandler($this->mockEntityManager, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(200, $response['code'], $response['content']);
    }

    /**
     * @return array
     */
    private function configureAWorkingResponse()
    {
        $data = [
            'MailMessage'      => '',
            'MessageId'        => '',
            'Timestamp'        => '',
            'TopicArn'         => 'fhhfjfj',
            'Token'            => 'token',
            'Type'             => 'test',
            'Signature'        => '',
            'SigningCertURL'   => '',
            'SignatureVersion' => '',
        ];
        $this->mockRequest->method('isMethod')->willReturn(true);

        $this->mockMessageValidator->method('isValid')->willReturn(true);

        $this->mockSnsClient = $this->createMock(SnsClient::class);

        return $data;
    }

    /**
     * @return array
     */
    private function getMailArray()
    {
        return [
            'messageId'        => 'message-id',
            'timestamp'        => '2016-08-01 00:00:00',
            'source'           => 'test@example.com',
            'sourceArn'        => 'new source arn',
            'sendingAccountId' => 'sending account id',
            'headers'          => 'new headers',
            'commonHeaders'    => 'New common headers',
        ];
    }

    /**
     * @return array
     */
    private function getBouncedNotificationMessage()
    {
        return [
            'mail'   => $this->getMailArray(),
            'bounce' => [
                'bouncedRecipients' => [
                    [
                        'emailAddress'   => 'test_recipient@example.com',
                        'status'         => 'status',
                        'diagnosticCode' => 'diagnostic code',
                        'action'         => 'the action to take',
                    ],
                ],
                'timestamp'     => '2016-08-01 00:00:00',
                'bounceType'    => 'type of bounce',
                'bounceSubType' => 'sub type of bounce',
                'feedbackId'    => 'the id of the feedback',
                'reportingMta'  => 'the MTA that reported the bounce',
            ],
            'notificationType' => NotificationHandler::MESSAGE_TYPE_BOUNCE,
        ];
    }

    /**
     * @return array
     */
    private function getComplainedNotificationMessage()
    {
        return [
            'mail'      => $this->getMailArray(),
            'complaint' => [
                'complainedRecipients' => [
                    [
                        'emailAddress'   => 'test_recipient@example.com',
                        'status'         => 'status',
                        'diagnosticCode' => 'diagnostic code',
                        'action'         => 'the action to take',
                    ],
                ],
                'timestamp'             => '2016-08-01 00:00:00',
                'userAgent'             => 'the user agent',
                'complaintFeedbackType' => 'complaint feedback type',
                'feedbackId'            => 'the id of the feedback',
                'arrivalDate'           => '2016-08-01 00:00:00',
            ],
            'notificationType' => NotificationHandler::MESSAGE_TYPE_COMPLAINT,
        ];
    }

    /**
     * @return array
     */
    private function getDeliveredNotificationMessage()
    {
        return [
            'mail'     => $this->getMailArray(),
            'delivery' => [
                'recipients' => [
                    'test_recipient@example.com',
                ],
                'timestamp'            => '2016-08-01 00:00:00',
                'processingTimeMillis' => 1234,
                'smtpResponse'         => 'smtp response',
                'reportingMta'         => 'reporting MTA',
            ],
            'notificationType' => NotificationHandler::MESSAGE_TYPE_DELIVERY,
        ];
    }

    private function mockNullMailMessage()
    {
        $mockMailMessageRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $mockMailMessageRepository->method('__call')->with('findOneByMessageId')->willReturn(null);
        $this->mockEntityManager->method('getRepository')->willReturn($mockMailMessageRepository);
    }
}
