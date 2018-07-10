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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Set the credentials in the command.
 */
class SetCommandsCredentialsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $credentialsServiceName = $container->getParameter('shq_aws_ses_monitor.aws_config')['credentials_service_name'];
        $credentials            = $container->getDefinition($credentialsServiceName);

        //$bouncesConfiguration = $container->getParameter('shq_aws_ses_monitor.bounces');
        //$complaintsConfiguration = $container->getParameter('shq_aws_ses_monitor.complaints');
        //$deliveriesConfiguration = $container->getParameter('shq_aws_ses_monitor.deliveries');

        foreach ($container->findTaggedServiceIds('shq_aws_ses_monitor.requires_credentials') as $service => $tags) {
            $commandDefinition = $container->getDefinition($service);

            $commandDefinition->addMethodCall('setCredentials', [$credentials/*, $bouncesConfiguration, $complaintsConfiguration, $deliveriesConfiguration*/]);
        }
    }
}
