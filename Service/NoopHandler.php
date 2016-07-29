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
 * A dummy handler that ever returns status 200 - Success.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */
class NoopHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request, Credentials $credentials)
    {
        return 200;
    }
}
