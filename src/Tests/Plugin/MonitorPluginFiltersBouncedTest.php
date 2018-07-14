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
class MonitorPluginFiltersBouncedTest extends MonitorFilterPluginTestAbstract
{
    /** @var array $complaintsConfig */
    private $complaintsConfig;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->complaintsConfig = $this->getDefaultComplaintsConfig();

        // Disable the filtering of complaints to not make it interfere with bounces filtering testing
        $this->complaintsConfig['filter']['enabled'] = false;
    }

    public function testBouncedFilterDisabledNoOneIsRemoved()
    {
        $bouncesConfig                      = $this->getDefaultBouncesConfig();
        $bouncesConfig['filter']['enabled'] = false;

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $bouncesConfig, $this->complaintsConfig);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }

    public function testBouncedForceSendNoOneIsRemoved()
    {
        $bouncesConfig                         = $this->getDefaultBouncesConfig();
        $bouncesConfig['filter']['force_send'] = true;

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $bouncesConfig, $this->complaintsConfig);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmNoOneRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmNoOneRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }

    public function testHardBouncesAreRemoved()
    {
        $bouncesConfig                          = $this->getDefaultBouncesConfig();
        $bouncesConfig['filter']['max_bounces'] = 2;

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $bouncesConfig, $this->complaintsConfig);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmHardBouncedRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmHardBouncedRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmHardBouncedRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }

    public function testSoftBouncesCountsAsHardAndAreRemoved()
    {
        $bouncesConfig                           = $this->getDefaultBouncesConfig();
        $bouncesConfig['filter']['soft_as_hard'] = 2;

        $filter = new MonitorFilterPlugin($this->mockEmailStatusManager, $bouncesConfig, $this->complaintsConfig);

        $message = $this->getDefaultMessage();
        $message->expects(self::once())->method('setTo')->willReturnCallback([$this, 'confirmHardBouncedRemoved']);
        $message->expects(self::once())->method('setCc')->willReturnCallback([$this, 'confirmHardBouncedRemoved']);
        $message->expects(self::once())->method('setBcc')->willReturnCallback([$this, 'confirmHardBouncedRemoved']);

        $mockEvent = $this->createMock(\Swift_Events_SendEvent::class);
        $mockEvent->expects(self::once())->method('getMessage')->willReturn($message);

        $filter->beforeSendPerformed($mockEvent);
        $filter->sendPerformed($mockEvent);
    }
}
