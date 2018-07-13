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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email;
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
        $test     = [
            'email'                 => $this->createMock(Email::class),
            'mailMessage'           => $this->createMock(MailMessage::class),
            'notification'          => [
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

        self::assertSame($test['email'], $resource->getEmail());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['notification']['complaint']['timestamp'], $resource->getComplainedOn()->format('Y-m-d H:i:s'));
        self::assertSame($test['notification']['complaint']['feedbackId'], $resource->getFeedbackId());
        self::assertSame($test['notification']['complaint']['userAgent'], $resource->getUserAgent());
        self::assertSame($test['notification']['complaint']['complaintFeedbackType'], $resource->getComplaintFeedbackType());
        self::assertSame($test['notification']['complaint']['arrivalDate'], $resource->getArrivalDate()->format('Y-m-d H:i:s'));
    }
}
