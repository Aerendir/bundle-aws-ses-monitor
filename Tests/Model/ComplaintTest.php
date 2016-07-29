<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\MailMessage;

/**
 * Tests the Complaint entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class ComplaintTest extends \PHPUnit_Framework_TestCase
{
    public function testComplaint()
    {
        $resource = new Complaint();
        $test     = [
            'email'                 => 'test@example.com',
            'mailMessage'           => $this->createMock(MailMessage::class),
            'complainedOn'          => $this->createMock(\DateTime::class),
            'feedbackId'            => 'feedback-id-from-amazon-ses',
            'userAgent'             => 'test-user-agent',
            'complaintFeedbackType' => 'test-type',
            'arrivalDate'           => $this->createMock(\DateTime::class),
        ];

        $resource->setEmailAddress($test['email'])
            ->setMailMessage($test['mailMessage'])
            ->setComplainedOn($test['complainedOn'])
            ->setFeedbackId($test['feedbackId'])
            ->setUserAgent($test['userAgent'])
            ->setComplaintFeedbackType($test['complaintFeedbackType'])
            ->setArrivalDate($test['arrivalDate']);

        $this->assertNull($resource->getId());
        $this->assertSame($test['email'], $resource->getEmailAddress());
        $this->assertSame($test['mailMessage'], $resource->getMailMessage());
        $this->assertSame($test['complainedOn'], $resource->getComplainedOn());
        $this->assertSame($test['feedbackId'], $resource->getFeedbackId());
        $this->assertSame($test['userAgent'], $resource->getUserAgent());
        $this->assertSame($test['complaintFeedbackType'], $resource->getComplaintFeedbackType());
        $this->assertSame($test['arrivalDate'], $resource->getArrivalDate());
    }
}
