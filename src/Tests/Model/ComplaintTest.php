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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;

/**
 * Tests the Complaint entity.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class ComplaintTest extends TestCase
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

        $resource->setEmail($test['email'])
            ->setMailMessage($test['mailMessage'])
            ->setComplainedOn($test['complainedOn'])
            ->setFeedbackId($test['feedbackId'])
            ->setUserAgent($test['userAgent'])
            ->setComplaintFeedbackType($test['complaintFeedbackType'])
            ->setArrivalDate($test['arrivalDate']);

        self::assertNull($resource->getId());
        self::assertSame($test['email'], $resource->getEmail());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['complainedOn'], $resource->getComplainedOn());
        self::assertSame($test['feedbackId'], $resource->getFeedbackId());
        self::assertSame($test['userAgent'], $resource->getUserAgent());
        self::assertSame($test['complaintFeedbackType'], $resource->getComplaintFeedbackType());
        self::assertSame($test['arrivalDate'], $resource->getArrivalDate());
        self::assertSame(null, $resource->getEmailStatus());
    }
}
