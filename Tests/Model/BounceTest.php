<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;

/**
 * Tests the Bounce entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class BounceTest extends \PHPUnit_Framework_TestCase
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

        $this->assertNull($resource->getId());
        $this->assertSame($test['email'], $resource->getEmailAddress());
        $this->assertSame($test['mailMessage'], $resource->getMailMessage());
        $this->assertSame($test['bouncedOn'], $resource->getBouncedOn());
        $this->assertSame($test['type'], $resource->getType());
        $this->assertSame($test['subType'], $resource->getSubType());
        $this->assertSame($test['feedbackId'], $resource->getFeedbackId());
        $this->assertSame($test['reportingMta'], $resource->getReportingMta());
        $this->assertSame($test['action'], $resource->getAction());
        $this->assertSame($test['status'], $resource->getStatus());
        $this->assertSame($test['diagnosticCode'], $resource->getDiagnosticCode());
        $this->assertTrue($resource->isPermanent());
    }
}
