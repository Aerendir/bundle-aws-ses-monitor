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
     */
    public function findIdentity(): array
    {
        return [];
    }
}
