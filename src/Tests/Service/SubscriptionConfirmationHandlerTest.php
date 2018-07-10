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
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\TopicRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\SubscriptionConfirmationHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class SubscriptionConfirmationHandlerTest extends TestCase
{
    /** @var Request */
    private $mockRequest;

    /** @var AwsClientFactory */
    private $mockAwsClientFactory;

    /** @var Credentials */
    private $mockCredentials;

    /** @var EntityManager */
    private $mockEntityManager;

    /** @var MessageValidator */
    private $mockMessageValidator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRequest          = $this->createMock(Request::class);
        $this->mockAwsClientFactory = $this->createMock(AwsClientFactory::class);
        $this->mockCredentials      = $this->createMock(Credentials::class);
        $this->mockEntityManager    = $this->createMock(EntityManager::class);
        $this->mockMessageValidator = $this->createMock(MessageValidator::class);
    }

    public function testReturn405IfMethodIsNotPost()
    {
        $this->mockRequest->method('isMethod')->willReturn(false);
        $handler  = new SubscriptionConfirmationHandler($this->mockEntityManager, $this->mockAwsClientFactory, $this->mockMessageValidator);
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

        $handler  = new SubscriptionConfirmationHandler($this->mockEntityManager, $this->mockAwsClientFactory, $this->mockMessageValidator);
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

        $handler  = new SubscriptionConfirmationHandler($this->mockEntityManager, $this->mockAwsClientFactory, $this->mockMessageValidator);
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

        $handler  = new SubscriptionConfirmationHandler($this->mockEntityManager, $this->mockAwsClientFactory, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(403, $response['code'], $response['content']);
    }

    public function testReturn404IfTopicIsNull()
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
            'Message'          => 'message',
        ];
        $encodedData = json_encode($data);

        $this->mockRequest->method('isMethod')->willReturn(true);
        $this->mockRequest->method('getContent')->willReturn($encodedData);
        $this->mockMessageValidator->method('isValid')->willReturn(true);

        $mockTopicRepository = $this->createMock(TopicRepository::class);
        $mockTopicRepository->method('findOneByTopicArn')->willReturn(null);
        $this->mockEntityManager->method('getRepository')->willReturn($mockTopicRepository);

        $handler  = new SubscriptionConfirmationHandler($this->mockEntityManager, $this->mockAwsClientFactory, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(404, $response['code'], $response['content']);
    }

    public function testHandleRequest()
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
            'Message'          => 'message',
        ];
        $encodedData = json_encode($data);

        $this->mockRequest->method('isMethod')->willReturn(true);
        $this->mockRequest->method('getContent')->willReturn($encodedData);

        $this->mockMessageValidator->method('isValid')->willReturn(true);

        $mockTopicRepository = $this->createMock(TopicRepository::class);
        $mockTopicRepository->method('findOneByTopicArn')->willReturn($this->createMock(Topic::class));
        $this->mockEntityManager->method('getRepository')->willReturn($mockTopicRepository);

        $mockSnsClient = $this->createMock(SnsClient::class);

        $this->mockAwsClientFactory->method('getSnsClient')->willReturn($mockSnsClient);

        $handler  = new SubscriptionConfirmationHandler($this->mockEntityManager, $this->mockAwsClientFactory, $this->mockMessageValidator);
        $response = $handler->handleRequest($this->mockRequest, $this->mockCredentials);
        self::assertSame(200, $response['code'], $response['content']);
    }
}
