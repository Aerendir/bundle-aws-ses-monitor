<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use Aws\Credentials\Credentials;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NoopHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class NoopHanlderTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlerTest()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockCredentials = $this->createMock(Credentials::class);

        $handler = new NoopHandler();

        $this->assertSame(200, $handler->handleRequest($mockRequest, $mockCredentials));
    }
}
