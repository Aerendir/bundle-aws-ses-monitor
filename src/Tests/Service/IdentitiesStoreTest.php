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

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\IdentitiesStore;

/**
 * {@inheritdoc}
 */
class IdentitiesStoreTest extends TestCase
{
    public function testIdentitiesStore()
    {
        $testIdentities = [
            'serendipityhq.com'       => [],
            'hello@serendipityhq.com' => [],
        ];

        $resource = new IdentitiesStore($testIdentities);

        self::assertEquals($testIdentities, $resource->getIdentities());
        self::assertEquals(array_keys($testIdentities), $resource->getIdentitiesList());
    }

    public function testGetIdentity()
    {
        $testIdentities = [
            'serendipityhq.com'       => ['attribute' => 'awesome'],
            'hello@serendipityhq.com' => [],
        ];

        $resource = new IdentitiesStore($testIdentities);

        self::assertEquals($testIdentities['serendipityhq.com'], $resource->getIdentity('serendipityhq.com'));
        self::assertEquals($testIdentities['serendipityhq.com']['attribute'], $resource->getIdentity('serendipityhq.com', 'attribute'));
    }

    public function testIdentityExists()
    {
        $testIdentities = [
            'serendipityhq.com'       => ['attribute' => 'awesome'],
            'hello@serendipityhq.com' => [],
        ];

        $resource = new IdentitiesStore($testIdentities);

        self::assertTrue($resource->identityExists('serendipityhq.com'));
        self::assertFalse($resource->identityExists('example.com'));
    }
}
