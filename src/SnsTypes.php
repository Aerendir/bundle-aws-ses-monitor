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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle;

/**
 * Defines the type of requests SNS can send.
 */
interface SnsTypes
{
    const HEADER_TYPE_NOTIFICATION          = 'Notification';
    const HEADER_TYPE_CONFIRM_SUBSCRIPTION  = 'SubscriptionConfirmation';
    const MESSAGE_TYPE_SUBSCRIPTION_SUCCESS = 'AmazonSnsSubscriptionSucceeded';
    const MESSAGE_TYPE_BOUNCE               = 'Bounce';
    const MESSAGE_TYPE_COMPLAINT            = 'Complaint';
    const MESSAGE_TYPE_DELIVERY             = 'Delivery';
}
