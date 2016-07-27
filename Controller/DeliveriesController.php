<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the Deliveries.
 */
class DeliveriesController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deliveriesAction(Request $request)
    {
        return $this->handleRequest($request, NotificationHandler::MESSAGE_TYPE_DELIVERY);
    }
}
