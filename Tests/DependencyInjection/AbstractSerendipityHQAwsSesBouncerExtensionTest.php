<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\AwsSesMonitorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the configuration of the bundle.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
abstract class AbstractSerendipityHQAwsSesBouncerExtensionTest extends TestCase
{
    private $extension;
    /** @var ContainerBuilder */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new AwsSesMonitorExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    /**
     * @param ContainerBuilder $container
     * @param $resource
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);

    public function testDefaultConfig()
    {
        $this->loadConfiguration($this->container, 'default_config');
        $this->container->compile();

        self::assertSame('orm', $this->container->getParameter('aws_ses_monitor.db_driver'));
        self::assertSame(null, $this->container->getParameter('aws_ses_monitor.model_manager_name'));

        /*
         *  Test AWS Config
         */
        self::assertTrue(is_array($this->container->getParameter('aws_ses_monitor.aws_config')));

        // Test Region
        self::assertSame('us-east-1', $this->container->getParameter('aws_ses_monitor.aws_config')['region']);

        // Test SES version
        self::assertSame('2010-12-01', $this->container->getParameter('aws_ses_monitor.aws_config')['ses_version']);

        // Test SNS version
        self::assertSame('2010-03-31', $this->container->getParameter('aws_ses_monitor.aws_config')['sns_version']);

        /*
         * Test mailers
         */
        self::assertSame(['default'], $this->container->getParameter('aws_ses_monitor.mailers'));

        /*
         * Test bounces configuration
         */
        self::assertSame('_aws_ses_monitor_bounces_endpoint', $this->container->getParameter('aws_ses_monitor.bounces')['topic']['endpoint']['route_name']);
        self::assertSame('http', $this->container->getParameter('aws_ses_monitor.bounces')['topic']['endpoint']['protocol']);
        self::assertSame('localhost.local', $this->container->getParameter('aws_ses_monitor.bounces')['topic']['endpoint']['host']);
        self::assertTrue($this->container->getParameter('aws_ses_monitor.bounces')['filter']['enabled']);
        self::assertFalse($this->container->getParameter('aws_ses_monitor.bounces')['filter']['soft_as_hard']);
        self::assertSame(5, $this->container->getParameter('aws_ses_monitor.bounces')['filter']['max_bounces']);
        self::assertSame('forever', $this->container->getParameter('aws_ses_monitor.bounces')['filter']['soft_blacklist_time']);
        self::assertSame('forever', $this->container->getParameter('aws_ses_monitor.bounces')['filter']['hard_blacklist_time']);
        self::assertFalse($this->container->getParameter('aws_ses_monitor.bounces')['filter']['force_send']);

        /*
         * Test complaints configuration
         */
        self::assertSame('_aws_ses_monitor_complaints_endpoint', $this->container->getParameter('aws_ses_monitor.complaints')['topic']['endpoint']['route_name']);
        self::assertSame('http', $this->container->getParameter('aws_ses_monitor.complaints')['topic']['endpoint']['protocol']);
        self::assertSame('localhost.local', $this->container->getParameter('aws_ses_monitor.complaints')['topic']['endpoint']['host']);
        self::assertTrue($this->container->getParameter('aws_ses_monitor.complaints')['filter']['enabled']);
        self::assertSame('forever', $this->container->getParameter('aws_ses_monitor.complaints')['filter']['blacklist_time']);
        self::assertFalse($this->container->getParameter('aws_ses_monitor.complaints')['filter']['force_send']);

        /*
         * Test deliveries configuration
         */
        self::assertTrue($this->container->getParameter('aws_ses_monitor.deliveries')['enabled']);
        self::assertSame('_aws_ses_monitor_deliveries_endpoint', $this->container->getParameter('aws_ses_monitor.deliveries')['topic']['endpoint']['route_name']);
        self::assertSame('http', $this->container->getParameter('aws_ses_monitor.deliveries')['topic']['endpoint']['protocol']);
        self::assertSame('localhost.local', $this->container->getParameter('aws_ses_monitor.deliveries')['topic']['endpoint']['host']);
    }

    public function testFilterDisabledByBothConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_disabled_by_both');
        $this->container->compile();

        self::assertFalse($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledByBothConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_both');
        $this->container->compile();

        self::assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledByBouncesConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_bounces');
        $this->container->compile();

        self::assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledByComplaintsConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_complaints');
        $this->container->compile();

        self::assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterHasPluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_both');
        $this->container->compile();

        self::assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
        self::assertTrue($this->container->hasDefinition('aws_ses_monitor.swift_mailer.filter'));

        $definition = $this->container->getDefinition('aws_ses_monitor.swift_mailer.filter');
        self::assertArrayHasKey('swiftmailer.default.plugin', $definition->getTags());
    }

    public function testFilterHasMultiplePluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_multiple_mailers');
        $this->container->compile();

        self::assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
        self::assertTrue($this->container->hasDefinition('aws_ses_monitor.swift_mailer.filter'));

        $definition = $this->container->getDefinition('aws_ses_monitor.swift_mailer.filter');
        self::assertCount(3, $definition->getTags());
        self::assertArrayHasKey('swiftmailer.default.plugin', $definition->getTags());
        self::assertArrayHasKey('swiftmailer.second.plugin', $definition->getTags());
        self::assertArrayHasKey('swiftmailer.third.plugin', $definition->getTags());
    }
}
