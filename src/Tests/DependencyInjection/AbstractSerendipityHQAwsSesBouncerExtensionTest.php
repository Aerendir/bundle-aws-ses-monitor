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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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
        self::assertSame('eu-west-1', $this->container->getParameter('shq_aws_ses_monitor.aws_config')['region']);

        // Test SES version
        self::assertSame('2010-12-01', $this->container->getParameter('shq_aws_ses_monitor.aws_config')['ses_version']);

        // Test SNS version
        self::assertSame('2010-03-31', $this->container->getParameter('shq_aws_ses_monitor.aws_config')['sns_version']);

        /*
         * Test mailers
         */
        self::assertSame(['default'], $this->container->getParameter('shq_aws_ses_monitor.mailers'));

        /*
         * Test endpoint configuration
         */
        self::assertSame('https', $this->container->getParameter('shq_aws_ses_monitor.endpoint')['scheme']);
        self::assertSame('localhost.local', $this->container->getParameter('shq_aws_ses_monitor.endpoint')['host']);

        /*
         * Test bounces configuration
         */
        self::assertTrue($this->container->getParameter('shq_aws_ses_monitor.bounces')['track']);
        self::assertSame('dummy-bounces-topic', $this->container->getParameter('shq_aws_ses_monitor.bounces')['topic']);
        self::assertFalse($this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['soft_as_hard']);
        self::assertSame(5, $this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['max_bounces']);
        self::assertSame('forever', $this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['soft_blacklist_time']);
        self::assertSame('forever', $this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['hard_blacklist_time']);
        self::assertFalse($this->container->getParameter('shq_aws_ses_monitor.bounces')['filter']['force_send']);

        /*
         * Test complaints configuration
         */
        self::assertTrue($this->container->getParameter('shq_aws_ses_monitor.complaints')['track']);
        self::assertSame('dummy-complaints-topic', $this->container->getParameter('shq_aws_ses_monitor.complaints')['topic']);
        self::assertSame('forever', $this->container->getParameter('shq_aws_ses_monitor.complaints')['filter']['blacklist_time']);
        self::assertFalse($this->container->getParameter('shq_aws_ses_monitor.complaints')['filter']['force_send']);

        /*
         * Test deliveries configuration
         */
        self::assertTrue($this->container->getParameter('shq_aws_ses_monitor.deliveries')['track']);
        self::assertSame('dummy-deliveries-topic', $this->container->getParameter('shq_aws_ses_monitor.deliveries')['topic']);
    }

    public function testTrackingDisabledDoesntRequireTopicBounces()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_no_topic_bounces');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have not enabled the tracking of "bounces" but you have anyway set the name of the topic. Either remove the name of the topic at path "bounces.topic" or enabled the tracking of "bounces" setting "bounces.track" to "true"');
        $this->container->compile();
    }

    public function testTrackingDisabledDoesntRequireTopicComplaints()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_no_topic_complaints');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have not enabled the tracking of "complaints" but you have anyway set the name of the topic. Either remove the name of the topic at path "complaints.topic" or enabled the tracking of "complaints" setting "complaints.track" to "true"');
        $this->container->compile();
    }

    public function testTrackingDisabledDoesntRequireTopicDeliveries()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_no_topic_deliveries');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have not enabled the tracking of "deliveries" but you have anyway set the name of the topic. Either remove the name of the topic at path "deliveries.topic" or enabled the tracking of "deliveries" setting "deliveries.track" to "true"');
        $this->container->compile();
    }

    public function testTrackingEnabledRequiresTopicBounces()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_requires_topic_bounces');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have enabled the tracking of "bounces" but you have not set the name of the topic to use. Please, set the name of the topic at path "bounces.topic".');
        $this->container->compile();
    }

    public function testTrackingEnabledRequiresTopicComplaints()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_requires_topic_bounces');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have enabled the tracking of "bounces" but you have not set the name of the topic to use. Please, set the name of the topic at path "bounces.topic".');
        $this->container->compile();
    }

    public function testTrackingEnabledRequiresTopicDeliveries()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_requires_topic_bounces');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have enabled the tracking of "bounces" but you have not set the name of the topic to use. Please, set the name of the topic at path "bounces.topic".');
        $this->container->compile();
    }

    public function testTrackingDisabledForBothConfigFilterIsNotLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_for_both');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');

        self::assertFalse($bouncesConfig['track']);
        self::assertFalse($complaintsConfig['track']);

        // The filter isn't loaded at all
        self::assertArrayNotHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testTrackingEnabledForBothConfigFilterIsLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_for_both');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertTrue($bouncesConfig['track']);
        self::assertTrue($complaintsConfig['track']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testTrackingEnabledForBouncesOnlyConfigFilterIsLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_for_bounces');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertTrue($bouncesConfig['track']);
        self::assertFalse($complaintsConfig['track']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testTrackingEnabledForComplaintsOnlyConfigFilterIsLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_for_complaints');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.bounces');
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.complaints');
        self::assertFalse($bouncesConfig['track']);
        self::assertTrue($complaintsConfig['track']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    /**
     * @param ContainerBuilder $container
     * @param $resource
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);
}
