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
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines the minimum requirements of a MonitorHandler.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */
interface HandlerInterface
{
    /**
     * @param Request     $request
     * @param Credentials $credentials The AWS Credentials to use
     *
     * @return int
     */
    public function handleRequest(Request $request, Credentials $credentials);
}
