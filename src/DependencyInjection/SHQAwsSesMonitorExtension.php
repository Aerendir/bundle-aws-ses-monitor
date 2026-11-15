<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class SHQAwsSesMonitorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        // Set parameters in the container
        $container->setParameter('shq_aws_ses_monitor.endpoint', $config['endpoint']);
        // Deprecated and ignored: kept only for the applications that read it
        $container->setParameter('shq_aws_ses_monitor.mailers', $config['mailers']);
        $container->setParameter('shq_aws_ses_monitor.identities', $config['identities']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Enable the filter if required
        if ($this->requiresFilter($config['identities'])) {
            $loader->load('filter.yml');
        }
    }

    /**
     * Checks if at least one identity requires the filter of the recipients.
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
