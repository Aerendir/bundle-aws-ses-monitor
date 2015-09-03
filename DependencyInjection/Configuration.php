<?php

namespace Shivas\BouncerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shivas_bouncer');

        $supportedDrivers = array('orm');
        $supportedProtocols = array('HTTP', 'HTTPS', 'http', 'https');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->defaultValue('orm')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->arrayNode('aws_api_key')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('region')->isRequired()->defaultValue('us-east-1')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('bounce_endpoint')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route_name')->defaultValue('_shivasbouncerbundle_bounce_endpoint')->cannotBeEmpty()->end()
                        ->scalarNode('protocol')
                            ->validate()
                                ->ifNotInArray($supportedProtocols)
                                ->thenInvalid('The protocol %s is not supported. Please choose one of '.json_encode($supportedProtocols))
                            ->end()
                            ->defaultValue('http')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('host')->defaultValue('localhost.local')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('filter_not_permanent')->defaultFalse()->end()
                        ->arrayNode('mailer_name')
                            ->isRequired()
                            ->prototype('scalar')->end()
                            ->defaultValue(array('default'))
                        ->end()
                    ->end();

        return $treeBuilder;
    }
}
