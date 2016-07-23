<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface MonitorHandlerInterface
{
    /**
     * @param Request $request
     *
     * @return int
     */
    public function handleRequest(Request $request);
}
