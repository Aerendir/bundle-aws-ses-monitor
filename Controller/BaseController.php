<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Aws\Credentials\Credentials;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\HandlerFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base controller to handle the various other controllers.
 */
class BaseController extends Controller
{
    /**
     * @param Request $request
     * @param string  $notificationType The type of notification to handle (Delivery, Bounce or Complaint)
     *
     * @return Response
     */
    protected function handleRequest(Request $request, $notificationType)
    {
        /** @var HandlerFactory $factory */
        $factory = $this->get('aws_ses_monitor.handler.factory');

        $monitorHandler = $factory->buildHandler($request, $notificationType);
        $responseCode = $monitorHandler->handleRequest($request, $this->getCredentials());

        return new Response('', $responseCode);
    }

    /**
     * @return Credentials
     */
    protected function getCredentials()
    {
        $credentials = $this->getParameter('aws_ses_monitor.aws_config')['credentials_service_name'];

        return $this->get($credentials);
    }
}
