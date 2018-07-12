<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
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
     * @param Request        $request
     * @param HandlerFactory $factory
     *
     * @return Response
     */
    public function notifyAction(Request $request, HandlerFactory $factory)
    {
        $monitorHandler = $factory->buildHandler($request);
        $response       = $monitorHandler->handleRequest($request, $this->getCredentials());

        if (false === is_array($response)) {
            $response = [
                'content' => '',
                'code'    => $response,
            ];
        }

        return new Response($response['content'], $response['code']);
    }

    /**
     * @return Credentials
     */
    protected function getCredentials()
    {
        $credentials = $this->getParameter('shq_aws_ses_monitor.aws_config')['credentials_service_name'];

        return $this->get($credentials);
    }
}
