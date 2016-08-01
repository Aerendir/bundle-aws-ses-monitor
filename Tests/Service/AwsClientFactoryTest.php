<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\AwsClientFactory;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class AwsClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSesClient()
    {
        $config = [
            'region' => 'eu-west-1',
            'ses_version' => '2010-12-01'
        ];

        $mockCredentials = $this->createMock(Credentials::class);

        $factory = new AwsClientFactory($config);
        $result = $factory->getSesClient($mockCredentials);

        $this->assertInstanceOf(SesClient::class, $result);
        $this->assertSame($config['region'], $result->getRegion());
        $this->assertSame($config['ses_version'], $result->getApi()->getApiVersion());
    }

    public function testGetSnsClient()
    {
        $config = [
            'region' => 'eu-west-1',
            'sns_version' => '2010-03-31'
        ];

        $mockCredentials = $this->createMock(Credentials::class);

        $factory = new AwsClientFactory($config);
        $result = $factory->getSnsClient($mockCredentials);

        $this->assertInstanceOf(SnsClient::class, $result);
        $this->assertSame($config['region'], $result->getRegion());
        $this->assertSame($config['sns_version'], $result->getApi()->getApiVersion());
    }
}
