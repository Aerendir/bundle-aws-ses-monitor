<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Aws\Credentials\Credentials;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\HandlerFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to handle notifications.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class NotifyController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function notifyAction(Request $request)
    {
        /** @var HandlerFactory $factory */
        $factory = $this->get('aws_ses_monitor.handler.factory');

        $monitorHandler = $factory->buildHandler($request);
        $response       = $monitorHandler->handleRequest($request, $this->getCredentials());

        if (false === is_array($response)) {
            $response = [
                'content' => '',
                'code'    => $response
            ];
        }

        return new Response($response['content'], $response['code']);
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
