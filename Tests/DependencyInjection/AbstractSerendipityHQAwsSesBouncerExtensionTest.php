<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\DependencyInjection;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\AwsSesMonitorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);

    public function testFilterDisabledConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_disabled');
        $this->container->compile();

        $this->assertFalse($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterEnabledConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled');
        $this->container->compile();

        $this->assertTrue($this->container->has('aws_ses_monitor.swift_mailer.filter'));
    }

    public function testFilterHasPluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled');
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
