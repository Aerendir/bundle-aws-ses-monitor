<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class NoopHandler implements MonitorHandlerInterface
{
    /**
     * @param Request $request
     *
     * @return int
     */
    public function handleRequest(Request $request)
    {
        return 200;
    }
}
