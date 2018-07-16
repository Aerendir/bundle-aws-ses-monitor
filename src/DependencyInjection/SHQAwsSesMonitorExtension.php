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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * {@inheritdoc}
 */
class SHQAwsSesMonitorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        // Set parameters in the container
        $container->setParameter('shq_aws_ses_monitor.aws_config', $config['aws_config']);
        $container->setParameter('shq_aws_ses_monitor.mailers', $config['mailers']);
        $container->setParameter('shq_aws_ses_monitor.bounces', $config['bounces']);
        $container->setParameter('shq_aws_ses_monitor.complaints', $config['complaints']);
        $container->setParameter('shq_aws_ses_monitor.deliveries', $config['deliveries']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Enable the plugin if required
        if ($config['bounces']['filter']['enabled'] || $config['complaints']['filter']['enabled']) {
            $loader->load('filter.yml');

            $mailers = $config['mailers'];
            $filter  = $container->getDefinition(MonitorFilterPlugin::class);
            foreach ($mailers as $mailer) {
                $filter->addTag(sprintf('swiftmailer.%s.plugin', $mailer));
            }
        }
    }
}
