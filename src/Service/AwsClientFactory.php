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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;

/**
 * Creates clients for SES and SNS.
 *
 * @see https://aws.amazon.com/it/documentation/ses/
 * @see https://aws.amazon.com/it/documentation/sns/
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class AwsClientFactory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param Credentials $credentials
     *
     * @return SesClient
     */
    public function getSesClient(Credentials $credentials)
    {
        $config = $this->buildSesConfig($credentials);

        return new SesClient($config);
    }

    /**
     * @param Credentials $credentials
     *
     * @return SnsClient
     */
    public function getSnsClient(Credentials $credentials)
    {
        $config = $this->buildSnsConfig($credentials);

        return new SnsClient($config);
    }

    /**
     * @param Credentials $credentials
     *
     * @return array
     */
    private function buildSesConfig(Credentials $credentials)
    {
        return [
            'credentials' => $credentials,
            'region'      => $this->config['region'],
            'version'     => $this->config['ses_version'],
        ];
    }

    /**
     * @param Credentials $credentials
     *
     * @return array
     */
    private function buildSnsConfig(Credentials $credentials)
    {
        return [
            'credentials' => $credentials,
            'region'      => $this->config['region'],
            'version'     => $this->config['sns_version'],
        ];
    }
}
