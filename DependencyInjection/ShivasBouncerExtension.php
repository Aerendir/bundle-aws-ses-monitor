<?php

namespace Shivas\BouncerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ShivasBouncerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // load db_driver container configuration
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        if ($config['filter']['enabled']) { // if enabled - define filter
            $loader->load('filter.xml');

            $mailers = $config['filter']['mailer_name'];
            $filter = $container->getDefinition('shivas_bouncer.swift_mailer.filter');
            foreach ($mailers as $mailer) {
                $filter->addTag(sprintf('swiftmailer.%s.plugin', $mailer));
            }
        }

        $container->setParameter(sprintf('shivas_bouncer.backend_%s', $config['db_driver']), true);
        $container->setParameter('shivas_bouncer.driver', $config['db_driver']);
        $container->setParameter('shivas_bouncer.manager_name', $config['model_manager_name']);
        $container->setParameter('shivas_bouncer.bounce_endpoint', $config['bounce_endpoint']);
        $container->setParameter('shivas_bouncer.filter', $config['filter']);
        $container->setParameter('shivas_bouncer.filter.filter_not_permanent', $config['filter']['filter_not_permanent']);
        $container->setParameter('shivas_bouncer.aws_config', $config['aws_config']);
    }
}
