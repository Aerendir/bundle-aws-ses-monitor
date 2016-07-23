<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AwsSesMonitorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $modelDir = realpath(__DIR__ . '/Resources/config/doctrine/mappings');
        $mappings = array(
            $modelDir => 'SerendipityHQ\Bundle\AwsSesMonitorBundle\Model',
        );

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver(
                    $mappings,
                    array('aws_ses_monitor.model_manager_name'),
                    'aws_ses_monitor.backend_orm',
                    array('AwsSesMonitorBundle' => 'SerendipityHQ\Bundle\AwsSesMonitorBundle\Model')
                ));
        }
    }
}
