<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlSerendipityHQAwsSesBouncerExtensionTest extends AbstractSerendipityHQAwsSesBouncerExtensionTest
{
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/'));
        $loader->load($resource.'.yml');
    }
}
