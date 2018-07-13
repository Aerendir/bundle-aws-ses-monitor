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

use Aws\Sns\MessageValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper\MessageHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * {@inheritdoc}
 */
class MessageHelperTest extends TestCase
{
    /** @var MockObject $messageValidator */
    private $messageValidator;

    /** @var MessageHelper $messageHelper */
    private $messageHelper;

    /** @var array */
    private $data = [
        'Message' => [
            'a_field'       => 'of the message',
            'another_field' => 'of the message',
        ],
        'MessageId'        => '',
        'Timestamp'        => '',
        'TopicArn'         => '',
        'Type'             => '',
        'Signature'        => '',
        'SigningCertURL'   => '',
        'SignatureVersion' => '',
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->messageValidator = $this->createMock(MessageValidator::class);
        $this->messageHelper    = new MessageHelper($this->messageValidator);
    }

    /**
     * Tests the creation of a Message from the Request.
     */
    public function testMessageHelper()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getContent')->willReturn(json_encode($this->data));

        $message = $this->messageHelper->buildMessageFromRequest($mockRequest);

        self::assertSame($this->data, $message->toArray());

        return $message;
    }

    /**
     * @depends testMessageHelper
     */
    public function testValidateNotification()
    {
        $this->messageValidator->method('isValid')->willReturn(true);
        $message = func_get_arg(0);

        $result = $this->messageHelper->validateNotification($message);

        self::assertTrue($result);
    }
}
