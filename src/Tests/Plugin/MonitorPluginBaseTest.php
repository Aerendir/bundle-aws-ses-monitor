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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Plugin;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;

/**
 * Tests the monitor filter plugin.
 */
class MonitorPluginBaseTest extends MonitorFilterPluginTestAbstract
{
    public function testBeforeSendPerformedDoesntFilterIfNoRecipients()
    {
        $mockIdentities = $this->createMockIdentities($this->getDefaultBouncesConfig(), $this->getDefaultComplaintsConfig());
        $filter         = new MonitorFilterPlugin($this->mockEmailStatusManager, $mockIdentities);

        $mockMessage = $this->createMock(\Swift_Message::class);
        $mockMessage->expects(self::once())->method('getTo')->willReturn(null);
        $mockMessage->expects(self::once())->method('getCc')->willReturn(null);
        $mockMessage->expects(self::once())->method('getBcc')->willReturn(null);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($mockMessage);

        $filter->beforeSendPerformed($mockEvent);
    }
}
