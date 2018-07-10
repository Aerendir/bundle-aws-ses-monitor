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

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\HandlerFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NoopHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\SubscriptionConfirmationHandler;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class HandlerFactoryTest extends TestCase
{
    private $request;
    private $_em;
    private $aws;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = $this->getMockBuilder(Request::class)
            ->getMock();
        $this->request->headers = new HeaderBag();

        $this->aws = $this->getMockBuilder(AwsClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->_em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->_em
            ->expects(self::any())
            ->method('getRepository')
            ->will(self::returnValue($this->createMock(ObjectRepository::class)));
    }

    public function testNoopHandlerCreated()
    {
        $factory = new HandlerFactory($this->_em, $this->aws);

        /* @var \Symfony\Component\HttpFoundation\Request $request */
        $this->request->headers->set('x-amz-sns-message-type', 'test-fake');
        $object = $factory->buildHandler($this->request);
        self::assertInstanceOf(NoopHandler::class, $object);
    }

    public function testNotificationHandlerCreated()
    {
        $factory = new HandlerFactory($this->_em, $this->aws);

        /* @var \Symfony\Component\HttpFoundation\Request $request */
        $this->request->headers->set('x-amz-sns-message-type', NotificationHandler::HEADER_TYPE);
        $object = $factory->buildHandler($this->request);
        self::assertInstanceOf(NotificationHandler::class, $object);
    }

    public function testSubscriptionConfirmationHandlerCreated()
    {
        $factory = new HandlerFactory($this->_em, $this->aws);

        /* @var \Symfony\Component\HttpFoundation\Request $request */
        $this->request->headers->set('x-amz-sns-message-type', SubscriptionConfirmationHandler::HEADER_TYPE);
        $object = $factory->buildHandler($this->request);
        self::assertInstanceOf(SubscriptionConfirmationHandler::class, $object);
    }
}
