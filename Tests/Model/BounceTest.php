<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;

/**
 * Tests the Bounce entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class BounceTest extends TestCase
{
    public function testBounce()
    {
        $resource = new Bounce();
        $test     = [
            'email'          => 'test@example.com',
            'mailMessage'    => $this->createMock(MailMessage::class),
            'bouncedOn'      => $this->createMock(\DateTime::class),
            'type'           => 'Permanent',
            'subType'        => 'General',
            'feedbackId'     => 'feedback-id-from-amazon-ses',
            'reportingMta'   => 'test',
            'action'         => 'test-action',
            'status'         => 'test-status',
            'diagnosticCode' => 'test-diagnostic-code'
        ];

        $resource->setEmailAddress($test['email'])
            ->setMailMessage($test['mailMessage'])
            ->setBouncedOn($test['bouncedOn'])
            ->setType($test['type'])
            ->setSubType($test['subType'])
            ->setFeedbackId($test['feedbackId'])
            ->setReportingMta($test['reportingMta'])
            ->setAction($test['action'])
            ->setStatus($test['status'])
            ->setDiagnosticCode($test['diagnosticCode']);

        self::assertNull($resource->getId());
        self::assertSame($test['email'], $resource->getEmailAddress());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['bouncedOn'], $resource->getBouncedOn());
        self::assertSame($test['type'], $resource->getType());
        self::assertSame($test['subType'], $resource->getSubType());
        self::assertSame($test['feedbackId'], $resource->getFeedbackId());
        self::assertSame($test['reportingMta'], $resource->getReportingMta());
        self::assertSame($test['action'], $resource->getAction());
        self::assertSame($test['status'], $resource->getStatus());
        self::assertSame($test['diagnosticCode'], $resource->getDiagnosticCode());
        self::assertTrue($resource->isPermanent());
        self::assertSame(null, $resource->getEmailStatus());
    }
}
