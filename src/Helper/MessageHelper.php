<?php

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
use function Safe\json_decode;
use Symfony\Component\HttpFoundation\Request;

/**
 * Helps to create and validate the received message.
 *
 * @internal
 */
final class MessageHelper
{
    /** @var MessageValidator $messageValidator */
    private $messageValidator;

    /**
     * @param MessageValidator $messageValidator
     */
    public function __construct(MessageValidator $messageValidator)
    {
        $this->messageValidator = $messageValidator;
    }

    /**
     * @param Request $request
     *
     * @return Message
     *
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
     * @param Message $message
     *
     * @return bool
     *
     * @internal
     */
    public function validateNotification(Message $message): bool
    {
        return $this->messageValidator->isValid($message);
    }

    /**
     * @param Message $message
     *
     * @return array
     *
     * @internal
     */
    public function extractMessageData(Message $message): array
    {
        return json_decode($message->offsetGet('Message'), true, 512, JSON_THROW_ON_ERROR);
    }
}
