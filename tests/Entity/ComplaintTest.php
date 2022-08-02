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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;

/**
 * Tests the Complaint entity.
 */
final class ComplaintTest extends TestCase
{
    public function testComplaint(): void
    {
        $test = [
            'email'        => $this->createMock(EmailStatus::class),
            'mailMessage'  => $this->createMock(MailMessage::class),
            'notification' => [
                'complaint' => [
                    'timestamp'             => '2016-08-01 00:00:00',
                    'userAgent'             => 'test-user-agent',
                    'complaintFeedbackType' => 'test-type',
                    'feedbackId'            => 'feedback-id-from-amazon-ses',
                    'arrivalDate'           => '2016-08-01 00:00:00',
                ],
            ],
            'complainedOn'          => $this->createMock(\DateTime::class),
            'feedbackId'            => 'feedback-id-from-amazon-ses',
            'userAgent'             => 'test-user-agent',
            'complaintFeedbackType' => 'test-type',
            'arrivalDate'           => $this->createMock(\DateTime::class),
        ];
        $resource = Complaint::create($test['email'], $test['mailMessage'], $test['notification']);

        self::assertSame($test['email'], $resource->getEmailStatus());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['notification']['complaint']['timestamp'], $resource->getComplainedOn()->format('Y-m-d H:i:s'));
        self::assertSame($test['notification']['complaint']['feedbackId'], $resource->getFeedbackId());
        self::assertSame($test['notification']['complaint']['userAgent'], $resource->getUserAgent());
        self::assertSame($test['notification']['complaint']['complaintFeedbackType'], $resource->getComplaintFeedbackType());
        self::assertSame($test['notification']['complaint']['arrivalDate'], $resource->getArrivalDate()->format('Y-m-d H:i:s'));
    }
}
