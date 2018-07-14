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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\SnsTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Processes the request from AWS SNS handling it with the right handler.
 */
class RequestProcessor
{
    /** @var NotificationProcessor $snsNotificationProcessor */
    private $snsNotificationProcessor;

    /** @var SubscriptionProcessor $subscriptionHandler */
    private $subscriptionHandler;

    /**
     * @param NotificationProcessor $snsNotificationProcessor
     * @param SubscriptionProcessor $subscriptionHandler
     */
    public function __construct(
        NotificationProcessor $snsNotificationProcessor,
        SubscriptionProcessor $subscriptionHandler
    ) {
        $this->snsNotificationProcessor = $snsNotificationProcessor;
        $this->subscriptionHandler      = $subscriptionHandler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function processRequest(Request $request): Response
    {
        if (false === $request->isMethod('POST')) {
            return new Response('Only POST requests are accepted.', 405);
        }

        /**
         * @var string|null the type casting on Symfony side is incorrect and doesn't
         *                  report the method can return "null" as default behavior if
         *                  the key isn't found
         */
        $messageTypeHeader = $request->headers->get('x-amz-sns-message-type');

        if (null === $messageTypeHeader) {
            throw new BadRequestHttpException('This request is invalid');
        }

        switch ($messageTypeHeader) {
            case SnsTypes::HEADER_TYPE_NOTIFICATION:
                return $this->snsNotificationProcessor->processRequest($request);
            case SnsTypes::HEADER_TYPE_CONFIRM_SUBSCRIPTION:
                return $this->subscriptionHandler->processRequest($request);

            default:
                throw new \RuntimeException('We received a request with header "%s" but are not able to handle it. Please, add an handler to manage it.');
        }
    }
}
