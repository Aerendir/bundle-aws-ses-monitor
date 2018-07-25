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
