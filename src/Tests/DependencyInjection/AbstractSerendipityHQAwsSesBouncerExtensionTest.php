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
        $bouncesConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['bounces'];
        self::assertTrue($bouncesConfig['track']);
        self::assertSame('localhost.local-serendipityhq.com-ses-prod-bounces', $bouncesConfig['topic']);
        self::assertFalse($bouncesConfig['filter']['soft_as_hard']);
        self::assertSame(5, $bouncesConfig['filter']['max_bounces']);
        self::assertSame('forever', $bouncesConfig['filter']['soft_blacklist_time']);
        self::assertSame('forever', $bouncesConfig['filter']['hard_blacklist_time']);
        self::assertFalse($bouncesConfig['filter']['force_send']);

        /*
         * Test complaints configuration
         */
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['complaints'];
        self::assertTrue($complaintsConfig['track']);
        self::assertSame('localhost.local-serendipityhq.com-ses-prod-complaints', $complaintsConfig['topic']);
        self::assertSame('forever', $complaintsConfig['filter']['blacklist_time']);
        self::assertFalse($complaintsConfig['filter']['force_send']);

        /*
         * Test deliveries configuration
         */
        $deliveriesConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['deliveries'];
        self::assertTrue($deliveriesConfig['track']);
        self::assertSame('localhost.local-serendipityhq.com-ses-prod-deliveries', $deliveriesConfig['topic']);
    }

    public function testTrackingDisabledDoesntRequireTopicBounces()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_no_topic_bounces');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have not enabled the tracking of "bounces" for identity "serendipityhq.com" but you have anyway set the name of its topic. Either remove the name of the topic at path "shq_aws_ses_monitor.identities.serendipityhq.com.bounces.topic" or enabled the tracking setting "shq_aws_ses_monitor.identities.serendipityhq.com.bounces.track" to "true".');
        $this->container->compile();
    }

    public function testTrackingDisabledDoesntRequireTopicComplaints()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_no_topic_complaints');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have not enabled the tracking of "complaints" for identity "serendipityhq.com" but you have anyway set the name of its topic. Either remove the name of the topic at path "shq_aws_ses_monitor.identities.serendipityhq.com.complaints.topic" or enabled the tracking setting "shq_aws_ses_monitor.identities.serendipityhq.com.complaints.track" to "true".');
        $this->container->compile();
    }

    public function testTrackingDisabledDoesntRequireTopicDeliveries()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_no_topic_deliveries');

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('You have not enabled the tracking of "deliveries" for identity "serendipityhq.com" but you have anyway set the name of its topic. Either remove the name of the topic at path "shq_aws_ses_monitor.identities.serendipityhq.com.deliveries.topic" or enabled the tracking setting "shq_aws_ses_monitor.identities.serendipityhq.com.deliveries.track" to "true".');
        $this->container->compile();
    }

    public function testTrackingDisabledForBothConfigFilterIsNotLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_disabled_for_both');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['bounces'];
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['complaints'];

        self::assertFalse($bouncesConfig['track']);
        self::assertFalse($complaintsConfig['track']);

        // The filter isn't loaded at all
        self::assertArrayNotHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testTrackingEnabledForBothConfigFilterIsLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_for_both');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['bounces'];
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['complaints'];

        self::assertTrue($bouncesConfig['track']);
        self::assertTrue($complaintsConfig['track']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testTrackingEnabledForBouncesOnlyConfigFilterIsLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_for_bounces');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['bounces'];
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['complaints'];

        self::assertTrue($bouncesConfig['track']);
        self::assertFalse($complaintsConfig['track']);

        // The filter was loaded (but also removed by the container)
        self::assertArrayHasKey(MonitorFilterPlugin::class, $this->container->getRemovedIds());
    }

    public function testTrackingEnabledForComplaintsOnlyConfigFilterIsLoaded()
    {
        $this->loadConfiguration($this->container, 'tracking_enabled_for_complaints');
        $this->container->compile();

        $bouncesConfig    = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['bounces'];
        $complaintsConfig = $this->container->getParameter('shq_aws_ses_monitor.identities')['serendipityhq.com']['complaints'];

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
