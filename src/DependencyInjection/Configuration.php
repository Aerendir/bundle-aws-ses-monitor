<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\DependencyInjection;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\IdentityGuesser;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * {@inheritdoc}
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    public const USE_DOMAIN = 'use_domain';

    /**
     * @var string
     */
    private const BOUNCES = 'bounces';

    /**
     * @var string
     */
    private const TRACK = 'track';

    /**
     * @var string
     */
    private const TOPIC = 'topic';

    /**
     * @var string
     */
    private const FOREVER = 'forever';

    /**
     * @var string
     */
    private const COMPLAINTS = 'complaints';

    /**
     * @var string
     */
    private const DELIVERIES = 'deliveries';

    /**
     * @var string
     */
    private const IDENTITIES = 'identities';

    /**
     * @var string
     */
    private const DOMAIN = 'domain';

    /** @var IdentityGuesser $identityGuesser */
    private $identityGuesser;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $this->identityGuesser = new IdentityGuesser();

        $treeBuilder = new TreeBuilder('shq_aws_ses_monitor');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
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
                ->arrayNode('identities')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('dkim')->defaultValue(true)->end()
                            ->booleanNode('feedback_forwarding')->defaultValue(true)->end()
                            ->booleanNode('headers_in_notification')->defaultValue(true)->end()
                            ->scalarNode('from_domain')->defaultNull()->end()
                            ->enumNode('on_mx_failure')->defaultValue('UseDefaultValue')->values(['UseDefaultValue', 'RejectMessage'])->end()
                        ->end()
                        ->append($this->bouncesNode())
                        ->append($this->complaintsNode())
                        ->append($this->deliveriesNode())
                    ->end()->end()
                ->end()
                ->validate()
                    ->ifTrue(function (array $tree): bool {
                        return $this->validateConfiguration($tree);
                    })
                    ->then(function (array $tree): array {
                        return $this->prepareConfiguration($tree);
                    })
                ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function bouncesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder(self::BOUNCES);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode(self::TRACK)->defaultTrue()->end()
                ->scalarNode(self::TOPIC)->defaultNull()->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('soft_as_hard')->defaultFalse()->end()
                        ->integerNode('max_bounces')->min(1)->defaultValue(5)->end()
                        ->scalarNode('soft_blacklist_time')->defaultValue(self::FOREVER)->end()
                        ->scalarNode('hard_blacklist_time')->defaultValue(self::FOREVER)->end()
                        ->booleanNode('force_send')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function complaintsNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder(self::COMPLAINTS);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode(self::TRACK)->defaultTrue()->end()
                ->scalarNode(self::TOPIC)->defaultNull()->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('blacklist_time')->defaultValue(self::FOREVER)->end()
                        ->booleanNode('force_send')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function deliveriesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder(self::DELIVERIES);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode(self::TRACK)->defaultTrue()->end()
                ->scalarNode(self::TOPIC)->defaultNull()->end()
            ->end();

        return $rootNode;
    }

    /**
     * @param array $tree
     *
     * @return bool
     */
    private function validateConfiguration(array $tree): bool
    {
        if ((\is_array($tree[self::IDENTITIES]) || $tree[self::IDENTITIES] instanceof \Countable ? \count($tree[self::IDENTITIES]) : 0) < 1) {
            throw new InvalidConfigurationException('You have to configure at least one identity you want be managed. Please, set it in path "shq_aws_monitor.identities".');
        }

        // Ensure the identities are in lowercase as they are anyway transformed in lowercase by Amazon
        // and we also need them in lowercase to make accurate checks on domain identities
        foreach ($tree[self::IDENTITIES] as $identity => $config) {
            $lowerIdentity = \strtolower($identity);
            unset($tree[self::IDENTITIES][$identity]);
            $tree[self::IDENTITIES][$lowerIdentity] = $config;
        }

        foreach ($tree[self::IDENTITIES] as $identity => $config) {
            $this->validateIdentity($identity, $config, $tree[self::IDENTITIES]);
        }

        return true;
    }

    /**
     * @param string $identity
     * @param array  $config
     * @param array  $identities
     */
    private function validateIdentity(string $identity, array $config, array $identities): void
    {
        $this->validateType($identity, self::BOUNCES, $config[self::BOUNCES], $identities);
        $this->validateType($identity, self::COMPLAINTS, $config[self::COMPLAINTS], $identities);
        $this->validateType($identity, self::DELIVERIES, $config[self::DELIVERIES], $identities);
    }

    /**
     * @param string $identity
     * @param string $type
     * @param array  $typeConfig
     * @param array  $identities
     */
    private function validateType(string $identity, string $type, array $typeConfig, array $identities): void
    {
        $track = $typeConfig[self::TRACK];
        $topic = $typeConfig[self::TOPIC];

        // If tracking is disabled but the topic name is passed anyway...
        if (false === $track && null !== $topic) {
            throw new InvalidConfigurationException(\Safe\sprintf('You have not enabled the tracking of "%s" for identity "%s" but you have anyway set the name of its topic. Either remove the name of the topic at path "shq_aws_ses_monitor.identities.%s.%s.topic" or enabled the tracking setting "shq_aws_ses_monitor.identities.%s.%s.track" to "true".', $type, $identity, $identity, $type, $identity, $type));
        }

        if (null !== $topic) {
            $this->validateTopic($identity, $type, $topic, $identities);
        }
    }

    /**
     * @param string $identity
     * @param string $type
     * @param string $topic
     * @param array  $identities
     */
    private function validateTopic(string $identity, string $type, string $topic, array $identities): void
    {
        $currentPath      = \Safe\sprintf('shq_aws_ses_monitor.identities.%s.%s.topic', $identity, $type);
        $checkCurrentPath = \Safe\sprintf('Check the configuration at path "%s".', $currentPath);
        $wantsToUseDomain = self::USE_DOMAIN === $topic;

        // If the identity isn't an email...
        if (false === $this->identityGuesser->isEmailIdentity($identity)) {
            // It is almost sure a domain: a domain cannot set the "use_domain" value for topic
            if ($wantsToUseDomain) {
                throw new InvalidConfigurationException(\Safe\sprintf('The identity "%s" is not an email. The value "%s" can be used only with email identities. %s', $identity, self::USE_DOMAIN, $checkCurrentPath));
            }

            // Is not an email and doesn't want to use the value "use_domain": we can exit the checks
            return;
        }

        // Based on previous checks, this is an email identity: get its parts
        $parts = $this->identityGuesser->getEmailParts($identity);

        // Check if the Domain identity was configured
        if (false === \array_key_exists($parts[self::DOMAIN], $identities)) {
            throw new InvalidConfigurationException(\Safe\sprintf('The domain "%s" of the email identity "%s" is NOT explicitly configured. You need to explicitly configure the domain identity "%s" to use its topic for the email identity "%s". %s', $parts[self::DOMAIN], $identity, $parts[self::DOMAIN], $identity, $checkCurrentPath));
        }

        // Check if the mailbox is a test one
        if ($this->identityGuesser->isTestEmail($parts['mailbox'])) {
            if ($wantsToUseDomain) {
                throw new InvalidConfigurationException(\Safe\sprintf('The email identity "%s" is for testing on development machines purposes only. You cannot set it to use the domain\'s topic that has to be used only in production. %s', $identity, $identity, $type, $checkCurrentPath));
            }

            // Check the topic used for this test email is not set for production identities
            foreach ($identities as $otherIdentity => $otherConfig) {
                // If this is isn't a production identity, it can also use the same endpoint of this one
                if (false === $this->identityGuesser->isProductionIdentity($otherIdentity)) {
                    continue;
                }

                if ($otherConfig['bunces'][self::TOPIC] === $topic) {
                    $bouncesPath = \Safe\sprintf('shq_aws_ses_monitor.identities.%s.bounces.topic', $otherIdentity);

                    throw new InvalidConfigurationException(\Safe\sprintf('The test email identity "%s" at path "%s" uses the same topic name of the production identity at path "%s". This is not allowed.', $identity, $currentPath, $bouncesPath));
                }

                // It is a production identity: check the topics are not the same of this one
                if ($otherConfig[self::COMPLAINTS][self::TOPIC] === $topic) {
                    $complaintsPath = \Safe\sprintf('shq_aws_ses_monitor.identities.%s.bounces.topic', $otherIdentity);

                    throw new InvalidConfigurationException(\Safe\sprintf('The test email identity "%s" at path "%s" uses the same topic name of the production identity at path "%s". This is not allowed.', $identity, $currentPath, $complaintsPath));
                }

                // It is a production identity: check the topics are not the same of this one
                if ($otherConfig[self::DELIVERIES][self::TOPIC] === $topic) {
                    $deliveriesPath = \Safe\sprintf('shq_aws_ses_monitor.identities.%s.bounces.topic', $otherIdentity);

                    throw new InvalidConfigurationException(\Safe\sprintf('The test email identity "%s" at path "%s" uses the same topic name of the production identity at path "%s". This is not allowed.', $identity, $currentPath, $deliveriesPath));
                }
            }
        }
    }

    /**
     * @param array $tree
     *
     * @return array
     */
    private function prepareConfiguration(array $tree): array
    {
        foreach ($tree[self::IDENTITIES] as $identity => $config) {
            // We have to make them again lowercase as in validation the $tree was not modified
            $lowerIdentity    = \strtolower($identity);
            $preparedIdentity = $this->prepareIdentity($tree['endpoint']['host'], $lowerIdentity, $config);
            unset($tree[self::IDENTITIES][$identity]);
            $tree[self::IDENTITIES][$lowerIdentity] = $preparedIdentity;
        }

        return $tree;
    }

    /**
     * @param string $host
     * @param string $identity
     * @param array  $config
     *
     * @return array
     */
    private function prepareIdentity(string $host, string $identity, array $config): array
    {
        $config[self::BOUNCES]    = $this->prepareNotification($host, $identity, self::BOUNCES, $config[self::BOUNCES]);
        $config[self::COMPLAINTS] = $this->prepareNotification($host, $identity, self::COMPLAINTS, $config[self::COMPLAINTS]);
        $config[self::DELIVERIES] = $this->prepareNotification($host, $identity, self::DELIVERIES, $config[self::DELIVERIES]);

        return $config;
    }

    /**
     * @param string $host
     * @param string $identity
     * @param string $type
     * @param array  $typeConfig
     *
     * @return array
     */
    private function prepareNotification(string $host, string $identity, string $type, array $typeConfig): array
    {
        if ($typeConfig[self::TRACK] && null === $typeConfig[self::TOPIC]) {
            $typeConfig[self::TOPIC] = $this->generateTopicName($host, $identity, $type);
        }

        return $typeConfig;
    }

    /**
     * @param string $host
     * @param string $identity
     * @param string $type
     *
     * @return string
     */
    private function generateTopicName(string $host, string $identity, string $type): string
    {
        $env = \strstr($identity, 'test') ? 'dev' : 'prod';

        return \Safe\sprintf('%s-%s-ses-%s-%s', $host, $identity, $env, $type);
    }
}
