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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\SHQAwsSesMonitorExtension;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the configuration of the bundle.
 */
abstract class AbstractSerendipityHQAwsSesBouncerExtensionTest extends TestCase
{
    /** @var SHQAwsSesMonitorExtension $extension */
    private $extension;

    /** @var ContainerBuilder $container */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new SHQAwsSesMonitorExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testDefaultConfig()
    {
        $this->loadConfiguration($this->container, 'default_config');
        $this->container->compile();

        /*
         *  Test AWS Config
         */
        self::assertTrue(is_array($this->container->getParameter('shq_aws_ses_monitor.aws_config')));

        // Test Region
        self::assertSame('us-east-1', $this->container->getParameter('shq_aws_ses_monitor.aws_config')['region']);

        // Test SES version
        self::assertSame('2010-12-01', $this->container->getParameter('shq_aws_ses_monitor.aws_config')['ses_version']);

        // Test SNS version
        self::assertSame('2010-03-31', $this->container->getParameter('shq_aws_ses_monitor.aws_config')['sns_version']);

        /*
         * Test mailers
         */
        self::assertSame(['default'], $this->container->getParameter('shq_aws_ses_monitor.mailers'));

        /*
         * Test bounces configuration
         */
        self::assertSame('_shq_aws_ses_monitor_bounces_endpoint', $this->container->getParameter('shq_aws_ses_monitor.bounces')['topic']['endpoint']['route_name']);
        self::assertSame('https', $this->container->getParameter('shq_aws_ses_monitor.bounces')['topic']['endpoint']['scheme']);
        self::assertSame('localhost.local', $this->container->getParameter('shq_aws_ses_monitor.bounces')['topic']['endpoint']['host']);
        self::assertTrue($this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['enabled']);
        self::assertFalse($this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['soft_as_hard']);
        self::assertSame(5, $this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['max_bounces']);
        self::assertSame('forever', $this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['soft_blacklist_time']);
        self::assertSame('forever', $this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['hard_blacklist_time']);
        self::assertFalse($this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['force_send']);

        /*
         * Test complaints configuration
         */
        self::assertSame('_shq_aws_ses_monitor_complaints_endpoint', $this->container->getParameter('shq_aws_ses_monitor.complaints')['topic']['endpoint']['route_name']);
        self::assertSame('https', $this->container->getParameter('shq_aws_ses_monitor.complaints')['topic']['endpoint']['scheme']);
        self::assertSame('localhost.local', $this->container->getParameter('shq_aws_ses_monitor.complaints')['topic']['endpoint']['host']);
        self::assertTrue($this->container->getParameter('shq_aws_ses_monitor.complaints')['filter']['enabled']);
        self::assertSame('forever', $this->container->getParameter('shq_aws_ses_monitor.complaints')['filter']['blacklist_time']);
        self::assertFalse($this->container->getParameter('shq_aws_ses_monitor.complaints')['filter']['force_send']);

        /*
         * Test deliveries configuration
         */
        self::assertTrue($this->container->getParameter('shq_aws_ses_monitor.deliveries')['enabled']);
        self::assertSame('_shq_aws_ses_monitor_deliveries_endpoint', $this->container->getParameter('shq_aws_ses_monitor.deliveries')['topic']['endpoint']['route_name']);
        self::assertSame('https', $this->container->getParameter('shq_aws_ses_monitor.deliveries')['topic']['endpoint']['scheme']);
        self::assertSame('localhost.local', $this->container->getParameter('shq_aws_ses_monitor.deliveries')['topic']['endpoint']['host']);
    }

    public function testFilterDisabledForBothConfig()
    {
        $this->loadConfiguration($this->container, 'filter_disabled_for_both');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertFalse($bouncesConfig['filter']['enabled']);
        self::assertFalse($complaintsConfig['filter']['enabled']);

        // The filter isn't loaded at all
        self::assertArrayNotHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testFilterEnabledForBothConfig()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_for_both');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertTrue($bouncesConfig['filter']['enabled']);
        self::assertTrue($complaintsConfig['filter']['enabled']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testFilterEnabledForBouncesOnlyConfig()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_for_bounces');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertTrue($bouncesConfig['filter']['enabled']);
        self::assertFalse($complaintsConfig['filter']['enabled']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testFilterEnabledForComplaintsOnlyConfig()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_for_complaints');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertFalse($bouncesConfig['filter']['enabled']);
        self::assertTrue($complaintsConfig['filter']['enabled']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    /**
     * @param ContainerBuilder $container
     * @param $resource
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);
}
