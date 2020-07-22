<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Processor;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\DeliveryNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class DeliveryNotificationHandlerTest extends TestCase
{
    public function testProcessNotification()
    {
        $test = [
            'delivery' => [
                'recipients' => [
                    'test_recipient@example.com',
                ],
                'timestamp'            => '2016-08-01 00:00:00',
                'processingTimeMillis' => 1234,
                'smtpResponse'         => 'smtp response',
                'reportingMta'         => 'reporting MTA',
            ],
        ];

        $mockEmailStatus  = $this->createMock(EmailStatus::class);
        $mockMailMessage  = $this->createMock(MailMessage::class);
        $mockEmailManager = $this->createMock(EmailStatusManager::class);
        $mockEmailManager->method('loadOrCreateEmailStatus')->willReturn($mockEmailStatus);

        $resource = new DeliveryNotificationHandler($mockEmailManager);

        $response = $resource->processNotification($test, $mockMailMessage);

        self::assertInstanceOf(Response::class, $response);
    }
}
