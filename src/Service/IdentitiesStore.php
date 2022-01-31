<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

/**
 * Contains all the configured identities.
 *
 * @internal
 */
final class IdentitiesStore
{
    /** @var array $identities */
    private $identities;

    public function __construct(array $identities)
    {
        $this->identities = $identities;
    }

    /**
     * @internal
     */
    public function getIdentities(): array
    {
        return $this->identities;
    }

    /**
     * @return array|bool|int|string
     *
     * @internal
     */
    public function getIdentity(string $identity, ?string $attribute = null)
    {
        $return = $this->getIdentities()[$identity];

        if (null !== $attribute) {
            return $return[$attribute];
        }

        return $return;
    }

    public function identityExists(string $identity): bool
    {
        return isset($this->getIdentities()[$identity]);
    }

    /**
     * @internal
     */
    public function getIdentitiesList(): array
    {
        return \array_keys($this->identities);
    }
}
