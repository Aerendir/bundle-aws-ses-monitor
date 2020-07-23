<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\RequestProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class EndpointController extends AbstractController
{
    /**
     * @param Request                $request
     * @param RequestProcessor       $processor
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     * @ codeCoverageIgnore
     */
    public function endpoint(Request $request, RequestProcessor $processor, EntityManagerInterface $entityManager): Response
    {
        $response = $processor->processRequest($request);

        $entityManager->flush();

        return $response;
    }
}
