<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * {@inheritdoc}
 *
 * Loads the Yaml configuration.
 */
class YamlAwsSesMonitorBundleExtensionTest extends AbstractSerendipityHQAwsSesBouncerExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixtures/'));
        $loader->load($resource . '.yml');
    }
}
