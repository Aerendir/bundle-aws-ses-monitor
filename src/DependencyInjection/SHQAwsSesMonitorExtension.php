<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use function Safe\sprintf;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Plugin\MonitorFilterPlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * {@inheritdoc}
 */
final class SHQAwsSesMonitorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        // Set parameters in the container
        $container->setParameter('shq_aws_ses_monitor.endpoint', $config['endpoint']);
        $container->setParameter('shq_aws_ses_monitor.mailers', $config['mailers']);
        $container->setParameter('shq_aws_ses_monitor.identities', $config['identities']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Enable the plugin if required
        if ($this->requiresFilter($config['identities'])) {
            $loader->load('filter.yml');

            $mailers = $config['mailers'];
            $filter  = $container->getDefinition(MonitorFilterPlugin::class);
            foreach ($mailers as $mailer) {
                $filter->addTag(sprintf('swiftmailer.%s.plugin', $mailer));
            }
        }
    }

    /**
     * Checks if at least one identity requires the SwiftMailer filter.
     */
    private function requiresFilter(array $identities): bool
    {
        foreach ($identities as $identityConfig) {
            if ($identityConfig['bounces']['track'] || $identityConfig['complaints']['track']) {
                return true;
            }
        }

        return false;
    }
}
