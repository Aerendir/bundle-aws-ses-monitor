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

use Aws\Sns\MessageValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates the handlers.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class HandlerFactory
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @var AwsClientFactory
     */
    private $awsFactory;

    /**
     * HandlerFactory constructor.
     *
     * @param EntityManager    $entityManager
     * @param AwsClientFactory $awsFactory
     */
    public function __construct(EntityManager $entityManager, AwsClientFactory $awsFactory)
    {
        $this->entityManager = $entityManager;
        $this->awsFactory    = $awsFactory;
    }

    /**
     * @param Request $request
     *
     * @return HandlerInterface
     */
    public function buildHandler(Request $request)
    {
        $headerType = $request->headers->get('x-amz-sns-message-type');

        switch ($headerType) {
            case NotificationHandler::HEADER_TYPE:
                return new NotificationHandler($this->entityManager, new MessageValidator());

            case SubscriptionConfirmationHandler::HEADER_TYPE:
                return new SubscriptionConfirmationHandler($this->entityManager, $this->awsFactory, new MessageValidator());

            default:
                return new NoopHandler(); // ignore all other types of messages for now
        }
    }
}
