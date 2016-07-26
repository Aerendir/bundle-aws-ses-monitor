<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritdoc}
 */
class AwsSesMonitorBundle extends Bundle
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
            ['aws_ses_monitor.model_manager_name'],
            'aws_ses_monitor.backend_orm',
            ['AwsSesMonitorBundle' => 'SerendipityHQ\Bundle\AwsSesMonitorBundle\Model']
        );
    }
}
