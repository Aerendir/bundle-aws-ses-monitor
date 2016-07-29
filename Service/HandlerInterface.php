<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
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
