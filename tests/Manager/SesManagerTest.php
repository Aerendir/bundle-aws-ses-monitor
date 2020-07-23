<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Manager;

use Aws\Ses\SesClient;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\SesManager;

/**
 * {@inheritdoc}
 */
class SesManagerTest extends TestCase
{
    public function testGetClient()
    {
        $mockClient = $this->createMock(SesClient::class);

        $resource = new SesManager($mockClient);

        self::assertSame($mockClient, $resource->getClient());
    }
}
