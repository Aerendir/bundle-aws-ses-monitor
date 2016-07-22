<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BounceController extends Controller
{
    public function bounceAction(Request $request)
    {
        $factory = $this->get('aws_ses_monitor.handler.factory');
        $bouncerHandler = $factory->buildHandler($request);
        $responseCode  = $bouncerHandler->handleRequest($request);
        return new Response('', $responseCode);
    }
}
