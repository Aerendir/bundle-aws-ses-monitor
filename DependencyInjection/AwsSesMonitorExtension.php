<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * {@inheritdoc}
 */
class AwsSesMonitorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters in the container
        $container->setParameter('aws_ses_monitor.db_driver', $config['db_driver']);
        $container->setParameter('aws_ses_monitor.manager_name', $config['model_manager_name']);
        $container->setParameter('aws_ses_monitor.aws_config', $config['aws_config']);
        $container->setParameter('aws_ses_monitor.mailers', $config['mailers']);
        $container->setParameter('aws_ses_monitor.bounces', $config['bounces']);
        $container->setParameter('aws_ses_monitor.complaints', $config['complaints']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        // load db_driver container configuration
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        // Enable the plugin if required
        if ($config['bounces']['filter']['enabled'] || $config['complaints']['filter']['enabled']) {
            $loader->load('filter.xml');

            $mailers = $config['mailers'];
            $filter = $container->getDefinition('aws_ses_monitor.swift_mailer.filter');
            foreach ($mailers as $mailer) {
                $filter->addTag(sprintf('swiftmailer.%s.plugin', $mailer));
            }
        }
    }
}
