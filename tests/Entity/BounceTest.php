<?php

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
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;

/**
 * Tests the Bounce entity.
 */
class BounceTest extends TestCase
{
    public function testBounce()
    {
        $test     = [
            'email'            => $this->createMock(EmailStatus::class),
            'mailMessage'      => $this->createMock(MailMessage::class),
            'bouncedRecipient' => [
                'emailAddress'   => 'test_recipient@example.com',
                'status'         => 'dummy_status',
                'diagnosticCode' => 'dummy_diagnostic_code',
                'action'         => 'dummy_action',
            ],
            'notification' => [
                'bounce' => [
                    'timestamp'     => '2018-07-12 00:00:00',
                    'bounceType'    => Bounce::TYPE_PERMANENT,
                    'bounceSubType' => Bounce::TYPE_TRANSIENT,
                    'feedbackId'    => 'dummy_id',
                    'reportingMta'  => 'reporting_mta',
                ],
            ],
        ];

        $resource = Bounce::create($test['email'], $test['mailMessage'], $test['bouncedRecipient'], $test['notification']);

        self::assertSame($test['email'], $resource->getEmailStatus());
        self::assertSame($test['mailMessage'], $resource->getMailMessage());
        self::assertSame($test['notification']['bounce']['timestamp'], $resource->getBouncedOn()->format('Y-m-d H:i:s'));
        self::assertSame($test['notification']['bounce']['bounceType'], $resource->getType());
        self::assertSame($test['notification']['bounce']['bounceSubType'], $resource->getSubType());
        self::assertSame($test['notification']['bounce']['feedbackId'], $resource->getFeedbackId());
        self::assertSame($test['notification']['bounce']['reportingMta'], $resource->getReportingMta());
        self::assertSame($test['bouncedRecipient']['action'], $resource->getAction());
        self::assertSame($test['bouncedRecipient']['status'], $resource->getStatus());
        self::assertSame($test['bouncedRecipient']['diagnosticCode'], $resource->getDiagnosticCode());
        self::assertTrue($resource->isPermanent());
    }
}
