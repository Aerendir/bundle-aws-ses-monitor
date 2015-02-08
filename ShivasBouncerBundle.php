<?php
namespace Shivas\BouncerBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShivasBouncerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $modelDir = realpath(__DIR__.'/Resources/config/doctrine/mappings');
        $mappings = array(
            $modelDir => 'Shivas\BouncerBundle\Model',
        );

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver(
                    $mappings,
                    array('shivas_bouncer.model_manager_name'),
                    'shivas_bouncer.backend_orm',
                    array('ShivasBouncerBundle' => 'Shivas\BouncerBundle\Model')
                ));
        }
    }
}
