<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle;

/**
 * Defines the type of requests SNS can send.
 */
final class SnsTypes
{
    /**
     * @var string
     */
    public const HEADER_TYPE_NOTIFICATION          = 'Notification';

    /**
     * @var string
     */
    public const HEADER_TYPE_CONFIRM_SUBSCRIPTION  = 'SubscriptionConfirmation';

    /**
     * @var string
     */
    public const MESSAGE_TYPE_SUBSCRIPTION_SUCCESS = 'AmazonSnsSubscriptionSucceeded';

    /**
     * @var string
     */
    public const MESSAGE_TYPE_BOUNCE               = 'Bounce';

    /**
     * @var string
     */
    public const MESSAGE_TYPE_COMPLAINT            = 'Complaint';

    /**
     * @var string
     */
    public const MESSAGE_TYPE_DELIVERY             = 'Delivery';
}
