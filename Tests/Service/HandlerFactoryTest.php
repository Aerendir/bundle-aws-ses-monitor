<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NoopHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\SubscriptionConfirmationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\AwsClientFactory;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\HandlerFactory;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * {@inheritdoc}
 */
class HandlerFactoryTest extends \PHPUnit_Framework_TestCase
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
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->createMock(ObjectRepository::class)));
    }

    public function testNoopHandlerCreated()
    {
        $factory = new HandlerFactory($this->_em, $this->aws);

        /* @var \Symfony\Component\HttpFoundation\Request $request */
        $this->request->headers->set('x-amz-sns-message-type', 'test-fake');
        $object = $factory->buildBouncesHandler($this->request);
        $this->assertInstanceOf(NoopHandler::class, $object);
    }

    public function testNotificationHandlerCreated()
    {
        $factory = new HandlerFactory($this->_em, $this->aws);

        /* @var \Symfony\Component\HttpFoundation\Request $request */
        $this->request->headers->set('x-amz-sns-message-type', NotificationHandler::HEADER_TYPE);
        $object = $factory->buildBouncesHandler($this->request);
        $this->assertInstanceOf(NotificationHandler::class, $object);
    }

    public function testSubscriptionConfirmationHandlerCreated()
    {
        $factory = new HandlerFactory($this->_em, $this->aws);

        /* @var \Symfony\Component\HttpFoundation\Request $request */
        $this->request->headers->set('x-amz-sns-message-type', SubscriptionConfirmationHandler::HEADER_TYPE);
        $object = $factory->buildBouncesHandler($this->request);
        $this->assertInstanceOf(SubscriptionConfirmationHandler::class, $object);
    }
}
