<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;

/**
 * Tests the Delivery entity.
 */
final class DeliveryTest extends TestCase
{
    public function testDelivery(): void
    {
        $test = [
            'email'        => $this->createMock(EmailStatus::class),
            'mailMessage'  => $this->createMock(MailMessage::class),
            'notification' => [
            'delivery' => [
                'timestamp'            => '2016-08-01 00:00:00',
                'processingTimeMillis' => 1234,
                'smtpResponse'         => 'smtp response',
                'reportingMta'         => 'reporting MTA',
            ],
                ],
        ];

        $resource = Delivery::create($test['email'], $test['mailMessage'], $test['notification']);

        self::assertSame($test['email'], $resource->getEmailStatus());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['notification']['delivery']['timestamp'], $resource->getDeliveredOn()->format('Y-m-d H:i:s'));
        self::assertSame($test['notification']['delivery']['processingTimeMillis'], $resource->getProcessingTimeMillis());
        self::assertSame($test['notification']['delivery']['smtpResponse'], $resource->getSmtpResponse());
        self::assertSame($test['notification']['delivery']['reportingMta'], $resource->getReportingMta());
    }
}
