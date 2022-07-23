<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\IdentitiesStore;

final class IdentitiesStoreTest extends TestCase
{
    public function testIdentitiesStore(): void
    {
        $testIdentities = [
            'serendipityhq.com'       => [],
            'hello@serendipityhq.com' => [],
        ];

        $resource = new IdentitiesStore($testIdentities);

        self::assertEquals($testIdentities, $resource->getIdentities());
        self::assertEquals(\array_keys($testIdentities), $resource->getIdentitiesList());
    }

    public function testGetIdentity(): void
    {
        $testIdentities = [
            'serendipityhq.com'       => ['attribute' => 'awesome'],
            'hello@serendipityhq.com' => [],
        ];

        $resource = new IdentitiesStore($testIdentities);

        self::assertEquals($testIdentities['serendipityhq.com'], $resource->getIdentity('serendipityhq.com'));
        self::assertEquals($testIdentities['serendipityhq.com']['attribute'], $resource->getIdentity('serendipityhq.com', 'attribute'));
    }

    public function testIdentityExists(): void
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
