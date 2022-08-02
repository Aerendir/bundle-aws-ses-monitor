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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Helper;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Symfony\Component\HttpFoundation\Request;

use function Safe\json_decode;

/**
 * Helps to create and validate the received message.
 *
 * @internal
 */
final class MessageHelper
{
    private MessageValidator $messageValidator;

    public function __construct(MessageValidator $messageValidator)
    {
        $this->messageValidator = $messageValidator;
    }

    /**
     * @internal
     */
    public function buildMessageFromRequest(Request $request): Message
    {
        /** @var string $content */
        $content = $request->getContent();
        $data    = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return new Message($data);
    }

    /**
     * @internal
     */
    public function validateNotification(Message $message): bool
    {
        return $this->messageValidator->isValid($message);
    }

    /**
     * @internal
     */
    public function extractMessageData(Message $message): array
    {
        return json_decode($message->offsetGet('Message'), true, 512, JSON_THROW_ON_ERROR);
    }
}
