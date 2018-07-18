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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Factory;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;

/**
 * Creates clients for SES and SNS.
 *
 * @see https://aws.amazon.com/it/documentation/ses/
 * @see https://aws.amazon.com/it/documentation/sns/
 */
class AwsClientFactory
{
    /** @var array */
    private $config;

    /** @var Credentials $credentials */
    private $credentials;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return Credentials
     */
    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    /**
     * @param Credentials $credentials
     */
    public function setCredentials(Credentials $credentials): void
    {
        $this->credentials = $credentials;
    }

    /**
     * @return SesClient
     */
    public function getSesClient(): SesClient
    {
        static $client = null;

        if (null === $client) {
            $sesConfig = $this->buildSesConfig();
            $client    = new SesClient($sesConfig);
        }

        return $client;
    }

    /**
     * @return SnsClient
     */
    public function getSnsClient(): SnsClient
    {
        static $client = null;

        if (null === $client) {
            $snsConfig = $this->buildSnsConfig();
            $client    = new SnsClient($snsConfig);
        }

        return $client;
    }

    /**
     * @return array
     */
    private function buildSesConfig(): array
    {
        return [
            'credentials' => $this->getCredentials(),
            'region'      => $this->config['region'],
            'version'     => $this->config['ses_version'],
        ];
    }

    /**
     * @return array
     */
    private function buildSnsConfig(): array
    {
        return [
            'credentials' => $this->getCredentials(),
            'region'      => $this->config['region'],
            'version'     => $this->config['sns_version'],
        ];
    }
}
