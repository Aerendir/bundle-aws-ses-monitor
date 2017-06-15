<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use Aws\Credentials\Credentials;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NoopHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class NoopHandlerTest extends TestCase
{
    public function testHandlerTest()
    {
        $mockRequest     = $this->createMock(Request::class);
        $mockCredentials = $this->createMock(Credentials::class);

        $handler = new NoopHandler();

        self::assertSame(200, $handler->handleRequest($mockRequest, $mockCredentials));
    }
}
