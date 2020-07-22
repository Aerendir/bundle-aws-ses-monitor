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
class SnsTypes
{
    const HEADER_TYPE_NOTIFICATION          = 'Notification';
    const HEADER_TYPE_CONFIRM_SUBSCRIPTION  = 'SubscriptionConfirmation';
    const MESSAGE_TYPE_SUBSCRIPTION_SUCCESS = 'AmazonSnsSubscriptionSucceeded';
    const MESSAGE_TYPE_BOUNCE               = 'Bounce';
    const MESSAGE_TYPE_COMPLAINT            = 'Complaint';
    const MESSAGE_TYPE_DELIVERY             = 'Delivery';
}
