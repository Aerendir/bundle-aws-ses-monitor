<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\DependencyInjection;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\AwsSesMonitorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the configuration of the bundle.
 */
abstract class AbstractSerendipityHQAwsSesBouncerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    /** @var ContainerBuilder */
    private $container;

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

        $this->assertSame('orm', $this->container->getParameter('aws_ses_monitor.db_driver'));
        $this->assertSame(null, $this->container->getParameter('aws_ses_monitor.model_manager_name'));

        /*
         *  Test AWS Config
         */
        $this->assertTrue(is_array($this->container->getParameter('aws_ses_monitor.aws_config')));

        // Test Region
        $this->assertSame('us-east-1', $this->container->getParameter('aws_ses_monitor.aws_config')['region']);

        // Test SES version
        $this->assertSame('2010-12-01', $this->container->getParameter('aws_ses_monitor.aws_config')['ses_version']);

        // Test SNS version
        $this->assertSame('2010-03-31', $this->container->getParameter('aws_ses_monitor.aws_config')['sns_version']);

        /*
         * Test mailers
         */
        $this->assertSame(['default'], $this->container->getParameter('aws_ses_monitor.mailers'));

        /*
         * Test bounces configuration
         */
        $this->assertSame('_aws_ses_monitor_bounces_endpoint', $this->container->getParameter('aws_ses_monitor.bounces')['topic']['endpoint']['route_name']);
        $this->assertSame('http', $this->container->getParameter('aws_ses_monitor.bounces')['topic']['endpoint']['protocol']);
        $this->assertSame('localhost.local', $this->container->getParameter('aws_ses_monitor.bounces')['topic']['endpoint']['host']);
        $this->assertTrue($this->container->getParameter('aws_ses_monitor.bounces')['filter']['enabled']);
        $this->assertFalse($this->container->getParameter('aws_ses_monitor.bounces')['filter']['soft_as_hard']);
        $this->assertSame(5, $this->container->getParameter('aws_ses_monitor.bounces')['filter']['max_bounces']);
        $this->assertSame('forever', $this->container->getParameter('aws_ses_monitor.bounces')['filter']['soft_blacklist_time']);
        $this->assertSame('forever', $this->container->getParameter('aws_ses_monitor.bounces')['filter']['hard_blacklist_time']);
        $this->assertFalse($this->container->getParameter('aws_ses_monitor.bounces')['filter']['force_send']);

        /*
         * Test complaints configuration
         */
        $this->assertSame('_aws_ses_monitor_complaints_endpoint', $this->container->getParameter('aws_ses_monitor.complaints')['topic']['endpoint']['route_name']);
        $this->assertSame('http', $this->container->getParameter('aws_ses_monitor.complaints')['topic']['endpoint']['protocol']);
        $this->assertSame('localhost.local', $this->container->getParameter('aws_ses_monitor.complaints')['topic']['endpoint']['host']);
        $this->assertTrue($this->container->getParameter('aws_ses_monitor.complaints')['filter']['enabled']);
        $this->assertSame('forever', $this->container->getParameter('aws_ses_monitor.complaints')['filter']['blacklist_time']);
        $this->assertFalse($this->container->getParameter('aws_ses_monitor.complaints')['filter']['force_send']);

        /*
         * Test deliveries configuration
         */
        $this->assertTrue($this->container->getParameter('aws_ses_monitor.deliveries')['enabled']);
        $this->assertSame('_aws_ses_monitor_deliveries_endpoint', $this->container->getParameter('aws_ses_monitor.deliveries')['topic']['endpoint']['route_name']);
        $this->assertSame('http', $this->container->getParameter('aws_ses_monitor.deliveries')['topic']['endpoint']['protocol']);
        $this->assertSame('localhost.local', $this->container->getParameter('aws_ses_monitor.deliveries')['topic']['endpoint']['host']);
    }

    public function testFilterDisabledByBothConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_disabled_by_both');
        $this->container->compile();

        $this->assertFalse($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledByBothConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_both');
        $this->container->compile();

        $this->assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledByBouncesConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_bounces');
        $this->container->compile();

        $this->assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledByComplaintsConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_complaints');
        $this->container->compile();

        $this->assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterHasPluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled_by_both');
        $this->container->compile();

        $this->assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
        $this->assertTrue($this->container->hasDefinition('aws_ses_monitor.swift_mailer.filter'));

        $definition = $this->container->getDefinition('aws_ses_monitor.swift_mailer.filter');
        $this->assertArrayHasKey('swiftmailer.default.plugin', $definition->getTags());
    }

    public function testFilterHasMultiplePluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_multiple_mailers');
        $this->container->compile();

        $this->assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
        $this->assertTrue($this->container->hasDefinition('aws_ses_monitor.swift_mailer.filter'));

        $definition = $this->container->getDefinition('aws_ses_monitor.swift_mailer.filter');
        $this->assertCount(3, $definition->getTags());
        $this->assertArrayHasKey('swiftmailer.default.plugin', $definition->getTags());
        $this->assertArrayHasKey('swiftmailer.second.plugin', $definition->getTags());
        $this->assertArrayHasKey('swiftmailer.third.plugin', $definition->getTags());
    }
}
