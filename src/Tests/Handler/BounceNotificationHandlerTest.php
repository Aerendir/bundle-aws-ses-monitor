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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\MailMessage;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Handler\BounceNotificationHandler;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class BounceNotificationHandlerTest extends TestCase
{
    public function testProcessNotification()
    {
        $test = [
            'bounce' => [
                'bouncedRecipients' => [
                    [
                        'emailAddress'   => 'test_recipient@example.com',
                        'status'         => 'status',
                        'diagnosticCode' => 'diagnostic code',
                        'action'         => 'the action to take',
                    ],
                ],
                'timestamp'     => '2016-08-01 00:00:00',
                'bounceType'    => 'type of bounce',
                'bounceSubType' => 'sub type of bounce',
                'feedbackId'    => 'the id of the feedback',
                'reportingMta'  => 'the MTA that reported the bounce',
            ],
        ];

        $mockEmailStatus  = $this->createMock(EmailStatus::class);
        $mockMailMessage  = $this->createMock(MailMessage::class);
        $mockEmailManager = $this->createMock(EmailStatusManager::class);
        $mockEmailManager->method('loadOrCreateEmailStatus')->willReturn($mockEmailStatus);

        $resource = new BounceNotificationHandler($mockEmailManager);

        $response = $resource->processNotification($test, $mockMailMessage);

        self::assertInstanceOf(Response::class, $response);
    }
}
