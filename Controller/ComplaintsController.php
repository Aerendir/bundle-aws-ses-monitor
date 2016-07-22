<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the bounces.
 */
class BouncesController extends Controller
{
    public function bouncesAction(Request $request)
    {
        $factory = $this->get('aws_ses_monitor.handler.factory');
        $monitorHandler = $factory->buildHandler($request);
        $responseCode  = $monitorHandler->handleRequest($request);
        return new Response('', $responseCode);
    }
}
