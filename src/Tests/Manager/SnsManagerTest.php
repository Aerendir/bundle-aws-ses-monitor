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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Util;

use Aws\Sns\SnsClient;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SnsManager;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * {@inheritdoc}
 */
class SnsManagerTest extends TestCase
{
    public function testGetClient()
    {
        $testEndpointConfig = [
            'scheme' => 'https',
            'host'   => 'serendipityhq.com',
        ];
        $mockClient  = $this->createMock(SnsClient::class);
        $mockContext = $this->createMock(RequestContext::class);
        $mockRouter  = $this->createMock(RouterInterface::class);

        $mockContext->expects(self::once())->method('setHost')->with($testEndpointConfig['host']);
        $mockContext->expects(self::once())->method('setScheme')->with($testEndpointConfig['scheme']);
        $mockRouter->expects(self::exactly(2))->method('getContext')->willReturn($mockContext);

        new SnsManager($testEndpointConfig, $mockClient, $mockRouter);
    }
}
