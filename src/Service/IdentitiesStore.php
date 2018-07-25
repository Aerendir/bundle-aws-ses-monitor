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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

/**
 * Contains all the configured identities.
 *
 * @internal
 */
class IdentitiesStore
{
    /** @var array $identities */
    private $identities;

    /**
     * @param array $identities
     */
    public function __construct(array $identities)
    {
        $this->identities = $identities;
    }

    /**
     * @return array
     *
     * @internal
     */
    public function getIdentities(): array
    {
        return $this->identities;
    }

    /**
     * @param string      $identity
     * @param string|null $attribute
     *
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

    /**
     * @param string $identity
     *
     * @return bool
     */
    public function identityExists(string $identity): bool
    {
        return isset($this->getIdentities()[$identity]);
    }

    /**
     * @return array
     *
     * @internal
     */
    public function getIdentitiesList(): array
    {
        return array_keys($this->identities);
    }
}
