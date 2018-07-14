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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Factory;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Factory\AwsClientFactory;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class AwsClientFactoryTest extends TestCase
{
    public function testGetSesClient()
    {
        $config = [
            'region'      => 'eu-west-1',
            'ses_version' => '2010-12-01',
        ];

        $mockCredentials = $this->createMock(Credentials::class);

        $factory = new AwsClientFactory($config);
        $factory->setCredentials($mockCredentials);
        $result  = $factory->getSesClient();

        self::assertInstanceOf(SesClient::class, $result);
        self::assertSame($config['region'], $result->getRegion());
        self::assertSame($config['ses_version'], $result->getApi()->getApiVersion());
    }

    public function testGetSnsClient()
    {
        $config = [
            'region'      => 'eu-west-1',
            'sns_version' => '2010-03-31',
        ];

        $mockCredentials = $this->createMock(Credentials::class);

        $factory = new AwsClientFactory($config);
        $factory->setCredentials($mockCredentials);
        $result  = $factory->getSnsClient();

        self::assertInstanceOf(SnsClient::class, $result);
        self::assertSame($config['region'], $result->getRegion());
        self::assertSame($config['sns_version'], $result->getApi()->getApiVersion());
    }
}
