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
 * Tests the monitor filter plugin handling of bounced emails.
 */
class MonitorPluginFiltersComplainedTest extends MonitorFilterPluginTestAbstract
{
    /** @var array $bouncesConfig */
    private $bouncesConfig;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->bouncesConfig = $this->getDefaultBouncesConfig();

        // Disable the filtering of bounces to not make it interfere with complaints filtering testing
        $this->bouncesConfig['track'] = false;
    }

    public function testComplainedFilterDisabledNoNoOneIsRemoved()
    {
        $complaintsConfig          = $this->getDefaultComplaintsConfig();
        $complaintsConfig['track'] = false;
        $mockIdentities            = $this->createMockIdentities($this->bouncesConfig, $complaintsConfig);

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $mockIdentities);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }

    public function testComplainedForceSendNoOneIsRemoved()
    {
        $complaintsConfig                         = $this->getDefaultComplaintsConfig();
        $complaintsConfig['filter']['force_send'] = true;
        $mockIdentities                           = $this->createMockIdentities($this->bouncesConfig, $complaintsConfig);

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $mockIdentities);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }

    public function testComplaintsAreRemoved()
    {
        $complaintsConfig = $this->getDefaultComplaintsConfig();
        $mockIdentities   = $this->createMockIdentities($this->bouncesConfig, $complaintsConfig);

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $mockIdentities);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmOnlyComplainedAreRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmOnlyComplainedAreRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmOnlyComplainedAreRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }
}
