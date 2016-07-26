<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\HandlerFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the Deliveries.
 */
class DeliveriesController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deliveriesAction(Request $request)
    {
        /** @var HandlerFactory $factory */
        $factory = $this->get('aws_ses_monitor.handler.factory');
        $credentials = $this->getParameter('aws_ses_monitor.aws_config')['credentials_service_name'];
        $credentials = $this->get($credentials);


        $monitorHandler = $factory->buildDeliveriesHandler($request);
        $responseCode = $monitorHandler->handleRequest($request, $credentials);

        return new Response('', $responseCode);
    }
}
