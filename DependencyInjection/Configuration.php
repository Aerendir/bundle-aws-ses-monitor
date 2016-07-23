<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

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
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode(self::getSupportedDrivers()))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->defaultValue('orm')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->arrayNode('aws_config')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('credentials_service_name')->defaultValue('client.aws.credentials')->cannotBeEmpty()->end()
                        ->scalarNode('region')->isRequired()->defaultValue('us-east-1')->cannotBeEmpty()->end()
                        ->scalarNode('ses_version')->defaultValue('2010-12-01')->cannotBeEmpty()->end()
                        ->scalarNode('sns_version')->defaultValue('2010-03-31')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('bounces_endpoint')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route_name')->defaultValue('_aws_monitor_bounces_endpoint')->cannotBeEmpty()->end()
                        ->scalarNode('protocol')
                            ->validate()
                                ->ifNotInArray(self::getSupportedProtocols())
                                ->thenInvalid('The protocol %s is not supported. Please choose one of '.json_encode(self::getSupportedProtocols()))
                                ->end()
                            ->defaultValue('http')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('host')->defaultValue('localhost.local')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('complaints_endpoint')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route_name')->defaultValue('_aws_monitor_complaints_endpoint')->cannotBeEmpty()->end()
                        ->scalarNode('protocol')
                            ->validate()
                                ->ifNotInArray(self::getSupportedProtocols())
                                ->thenInvalid('The protocol %s is not supported. Please choose one of '.json_encode(self::getSupportedProtocols()))
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
                        ->booleanNode('filter_not_blacklists')->defaultFalse()->end()
                        ->integerNode('number_of_bounces_for_blacklist')->min(1)->defaultValue(5)->end()
                        ->arrayNode('mailer_name')
                            ->isRequired()
                            ->prototype('scalar')->end()
                            ->defaultValue(['default'])
                        ->end()
                    ->end();

        return $treeBuilder;
    }
}
