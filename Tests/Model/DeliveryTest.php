<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;

/**
 * Tests the Delivery entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class DeliveryTest extends TestCase
{
    public function testDelivery()
    {
        $resource = new Delivery();
        $test     = [
            'email'                => 'test@example.com',
            'mailMessage'          => $this->createMock(MailMessage::class),
            'deliveredOn'          => $this->createMock(\DateTime::class),
            'processingTimeMillis' => 1000,
            'smtpResponse'         => 'test-user-agent',
            'reportingMta'         => 'test-type',
        ];

        $resource->setEmailAddress($test['email'])
            ->setMailMessage($test['mailMessage'])
            ->setDeliveredOn($test['deliveredOn'])
            ->setProcessingTimeMillis($test['processingTimeMillis'])
            ->setSmtpResponse($test['smtpResponse'])
            ->setReportingMta($test['reportingMta']);

        self::assertNull($resource->getId());
        self::assertSame($test['email'], $resource->getEmailAddress());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['deliveredOn'], $resource->getDeliveredOn());
        self::assertSame($test['processingTimeMillis'], $resource->getProcessingTimeMillis());
        self::assertSame($test['smtpResponse'], $resource->getSmtpResponse());
        self::assertSame($test['reportingMta'], $resource->getReportingMta());
        self::assertSame(null, $resource->getEmailStatus());
    }
}
