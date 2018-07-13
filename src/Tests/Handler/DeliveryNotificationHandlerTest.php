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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\DeliveryNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailManager;
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

        $mockEmail        = $this->createMock(Email::class);
        $mockMailMessage  = $this->createMock(MailMessage::class);
        $mockEmailManager = $this->createMock(EmailManager::class);
        $mockEmailManager->method('loadOrCreateEmail')->willReturn($mockEmail);

        $resource = new DeliveryNotificationHandler($mockEmailManager);

        $response = $resource->processNotification($test, $mockMailMessage);

        self::assertInstanceOf(Response::class, $response);
    }
}
