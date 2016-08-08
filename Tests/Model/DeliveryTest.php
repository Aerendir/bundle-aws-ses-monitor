<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;

/**
 * Tests the Delivery entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class DeliveryTest extends \PHPUnit_Framework_TestCase
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

        $this->assertNull($resource->getId());
        $this->assertSame($test['email'], $resource->getEmailAddress());
        $this->assertSame($test['mailMessage'], $resource->getMailMessage());
        $this->assertSame($test['deliveredOn'], $resource->getDeliveredOn());
        $this->assertSame($test['processingTimeMillis'], $resource->getProcessingTimeMillis());
        $this->assertSame($test['smtpResponse'], $resource->getSmtpResponse());
        $this->assertSame($test['reportingMta'], $resource->getReportingMta());
        $this->assertSame(null, $resource->getEmailStatus());
    }
}
