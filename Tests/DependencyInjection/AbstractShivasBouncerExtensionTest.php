<?php
namespace Shivas\BouncerBundle\Tests\DependencyInjection;

use Shivas\BouncerBundle\DependencyInjection\ShivasBouncerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractShivasBouncerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    /** @var ContainerBuilder */
    private $container;

    protected function setUp()
    {
        $this->extension = new ShivasBouncerExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);

    public function testFilterDisabledConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_disabled');
        $this->container->compile();

        $this->assertFalse($this->container->has('shivas_bouncer.swift_mailer.filter'));
    }

    public function testFilterEnabledConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled');
        $this->container->compile();

        $this->assertTrue($this->container->has('shivas_bouncer.swift_mailer.filter'));
    }

    public function testFilterHasPluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_enabled');
        $this->container->compile();

        $this->assertTrue($this->container->has('shivas_bouncer.swift_mailer.filter'));
        $this->assertTrue($this->container->hasDefinition('shivas_bouncer.swift_mailer.filter'));

        $definition = $this->container->getDefinition('shivas_bouncer.swift_mailer.filter');
        $this->assertArrayHasKey('swiftmailer.default.plugin', $definition->getTags());
    }

    public function testFilterHasMultiplePluginTagConfiguration()
    {
        $this->loadConfiguration($this->container, 'filter_multiple_mailers');
        $this->container->compile();

        $this->assertTrue($this->container->has('shivas_bouncer.swift_mailer.filter'));
        $this->assertTrue($this->container->hasDefinition('shivas_bouncer.swift_mailer.filter'));

        $definition = $this->container->getDefinition('shivas_bouncer.swift_mailer.filter');
        $this->assertCount(3, $definition->getTags());
        $this->assertArrayHasKey('swiftmailer.default.plugin', $definition->getTags());
        $this->assertArrayHasKey('swiftmailer.second.plugin', $definition->getTags());
        $this->assertArrayHasKey('swiftmailer.third.plugin', $definition->getTags());
    }


}
