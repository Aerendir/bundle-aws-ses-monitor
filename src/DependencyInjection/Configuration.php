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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
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
                        ->scalarNode('credentials_service_name')->defaultValue('Aws\Credentials\Credentials')->cannotBeEmpty()->end()
                        ->scalarNode('region')->defaultValue('eu-west-1')->cannotBeEmpty()->end()
                        ->scalarNode('ses_version')->defaultValue('2010-12-01')->cannotBeEmpty()->end()
                        ->scalarNode('sns_version')->defaultValue('2010-03-31')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('mailers')
                    ->prototype('scalar')->end()
                    ->defaultValue(['default'])
                ->end()
                ->arrayNode('endpoint')
                    ->isRequired()
                    ->children()
                        ->scalarNode('scheme')->defaultValue('https')->end()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('bounces')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('track')->defaultTrue()->end()
                        ->scalarNode('topic')->defaultNull()->end()
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
                        ->booleanNode('track')->defaultTrue()->end()
                        ->scalarNode('topic')->defaultNull()->end()
                        ->arrayNode('filter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('blacklist_time')->defaultValue('forever')->end()
                                ->booleanNode('force_send')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('deliveries')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('track')->defaultTrue()->end()
                        ->scalarNode('topic')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function (array $tree) {
                        return $this->validateConfiguration($tree);
                    })
                    ->then(function (array $tree) {
                        return $tree;
                    })
                ->end();

        return $treeBuilder;
    }

    /**
     * @param array $tree
     *
     * @return bool
     */
    private function validateConfiguration(array $tree): bool
    {
        $this->validateType('bounces', $tree);
        $this->validateType('complaints', $tree);
        $this->validateType('deliveries', $tree);

        return true;
    }

    /**
     * @param string $type
     * @param array  $tree
     */
    private function validateType(string $type, array $tree): void
    {
        $track = $tree[$type]['track'];
        $topic = $tree[$type]['topic'];

        // If tracking is enabled and topic is null...
        if (true === $track && null === $topic) {
            throw new InvalidConfigurationException(sprintf('You have enabled the tracking of "%s" but you have not set the name of the topic to use. Please, set the name of the topic at path "%s.topic".', $type, $type));
        }

        // If tracking is disabled but the topic name is passed anyway...
        if (false === $track && null !== $topic) {
            throw new InvalidConfigurationException(sprintf('You have not enabled the tracking of "%s" but you have anyway set the name of the topic. Either remove the name of the topic at path "%s.topic" or enabled the tracking of "%s" setting "%s.track" to "true".', $type, $type, $type, $type));
        }
    }
}
