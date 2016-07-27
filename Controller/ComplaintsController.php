<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\NotificationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the Complaints.
 */
class ComplaintsController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function complaintsAction(Request $request)
    {
        return $this->handleRequest($request, NotificationHandler::MESSAGE_TYPE_COMPLAINT);
    }
}
