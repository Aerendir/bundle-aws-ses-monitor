<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * The list of supported ORM drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return ['orm'];
    }

    /**
     * The list of supported protocols.
     *
     * @return array
     */
    public static function getSupportedProtocols()
    {
        return ['HTTP', 'HTTPS', 'http', 'https'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aws_ses_monitor');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray(self::getSupportedDrivers())
                        ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode(self::getSupportedDrivers()))
                    ->end()
                    ->cannotBeOverwritten()
                    ->defaultValue('orm')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->arrayNode('aws_config')
                    ->isRequired()
                    ->children()
                        ->scalarNode('credentials_service_name')->defaultValue('client.aws.credentials')->cannotBeEmpty()->end()
                        ->scalarNode('region')->defaultValue('us-east-1')->cannotBeEmpty()->end()
                        ->scalarNode('ses_version')->defaultValue('2010-12-01')->cannotBeEmpty()->end()
                        ->scalarNode('sns_version')->defaultValue('2010-03-31')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('mailers')
                    ->prototype('scalar')->end()
                    ->defaultValue(['default'])
                ->end()
                ->arrayNode('bounces')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->addEndpointSection('_aws_ses_monitor_bounces_endpoint'))
                        ->arrayNode('filter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('soft_as_hard')->defaultFalse()->end()
                                ->integerNode('max_bounces')->min(1)->defaultValue(5)->end()
                                ->scalarNode('soft_blacklist_time')->defaultValue('forever')->end()
                                ->scalarNode('hard_blacklist_time')->defaultValue('forever')->end()
                                ->booleanNode('force_send')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('complaints')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->addEndpointSection('_aws_ses_monitor_complaints_endpoint'))
                        ->arrayNode('filter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('blacklist_time')->defaultValue('forever')->end()
                                ->booleanNode('force_send')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }

    /**
     * Adds the section about endpoint configuration.
     *
     * @param string $endpoint_name The endpoint name to use
     *
     * @return ArrayNodeDefinition|NodeDefinition The root node (as an ArrayNodeDefinition when the type is 'array')
     */
    public function addEndpointSection($endpoint_name)
    {
        $builder = new TreeBuilder();
        $node = $builder->root('endpoint')->addDefaultsIfNotSet();

        $node
            ->children()
                ->scalarNode('route_name')->defaultValue($endpoint_name)->cannotBeEmpty()->end()
                ->scalarNode('protocol')
                    ->validate()
                    ->ifNotInArray(self::getSupportedProtocols())
                    ->thenInvalid('The protocol %s is not supported. Please choose one of ' . json_encode(self::getSupportedProtocols()))->end()
                    ->defaultValue('http')->cannotBeEmpty()
                ->end()
                ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $node;
    }
}
