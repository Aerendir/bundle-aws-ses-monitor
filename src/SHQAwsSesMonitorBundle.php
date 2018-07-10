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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection\CompilerPass\SetCommandsCredentialsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritdoc}
 *
 * @codeCoverageIgnore
 */
class SHQAwsSesMonitorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $modelDir = realpath(__DIR__ . '/Resources/config/doctrine/mappings');
        $mappings = [
            $modelDir => 'SerendipityHQ\Bundle\AwsSesMonitorBundle\Model',
        ];

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(
                $this->getXmlMappingDriver($mappings)
            );
        }

        $container->addCompilerPass(new SetCommandsCredentialsCompilerPass());
    }

    /**
     * @param array $mappings
     *
     * @return DoctrineOrmMappingsPass
     */
    private function getXmlMappingDriver(array $mappings)
    {
        return DoctrineOrmMappingsPass::createXmlMappingDriver(
            $mappings,
            ['shq_aws_ses_monitor.model_manager_name'],
            'shq_aws_ses_monitor.backend_orm',
            ['SHQAwsSesMonitorBundle' => 'SerendipityHQ\Bundle\AwsSesMonitorBundle\Model']
        );
    }
}
