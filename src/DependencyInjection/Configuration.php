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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('shq_aws_ses_monitor');

        $rootNode
            ->children()
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
                        ->append($this->addTopicSection('bounces'))
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
                        ->scalarNode('topic_name')->defaultValue('not_set')->cannotBeEmpty()->end()
                        ->append($this->addTopicSection('complaints'))
                        ->arrayNode('filter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('blacklist_time')->defaultValue('forever')->end()
                                ->booleanNode('force_send')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('deliveries')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('topic_name')->defaultValue('not_set')->cannotBeEmpty()->end()
                            ->append($this->addTopicSection('deliveries'))
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }

    /**
     * Adds the section about endpoint configuration.
     *
     * @param string $type The type of notification configuring
     *
     * @return ArrayNodeDefinition|NodeDefinition The root node (as an ArrayNodeDefinition when the type is 'array')
     */
    public function addTopicSection($type)
    {
        $builder   = new TreeBuilder();
        $node      = $builder->root('topic')->addDefaultsIfNotSet();
        $routeName = sprintf('_shq_aws_ses_monitor_%s_endpoint', $type);

        $node
            ->children()
                ->scalarNode('name')->defaultValue('not_set')->cannotBeEmpty()->end()
                ->arrayNode('endpoint')
                    ->children()
                        ->scalarNode('route_name')->defaultValue($routeName)->cannotBeEmpty()->end()
                        ->scalarNode('scheme')->defaultValue('https')->end()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
