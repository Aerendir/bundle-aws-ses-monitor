<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the Complaints.
 */
class ComplaintsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function complaintsAction(Request $request)
    {
        $factory = $this->get('aws_ses_monitor.handler.factory');
        $monitorHandler = $factory->buildHandler($request);
        $responseCode = $monitorHandler->handleRequest($request);

        return new Response('', $responseCode);
    }
}
