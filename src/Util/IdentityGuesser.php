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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Util;

/**
 * Provides some static methods to better understand
 * the nature of a configured identity.
 *
 * @internal
 */
class IdentityGuesser
{
    /** @var string The name of the testing email Identity */
    public const TEST_MAILBOX = 'test_aws';

    /**
     * @param string $identity
     *
     * @return array
     *
     * @internal
     */
    public function getEmailParts(string $identity): array
    {
        // Here we explicitly check for email as this way it intercepts also wrong
        // $identities like, for example, "an_identity" that is not an email not a domain
        if (false === $this->isEmailIdentity($identity)) {
            throw new \InvalidArgumentException(sprintf('The value "%s" is not an Email identity.', $identity));
        }

        $parts = explode('@', $identity);

        return [
            'mailbox' => $parts[0],
            'domain'  => $parts[1],
        ];
    }

    /**
     * @param string $identity
     *
     * @return bool
     */
    public function isDomainIdentity(string $identity): bool
    {
        return false === $this->isEmailIdentity($identity);
    }

    /**
     * @param string $identity
     *
     * @return bool
     *
     * @internal
     */
    public function isEmailIdentity(string $identity): bool
    {
        return (bool) strstr($identity, '@');
    }

    /**
     * @param string $identity
     *
     * @return bool
     *
     * @internal
     */
    public function isProductionIdentity(string $identity): bool
    {
        // If is not an email identity, then is for sure an identity to be used in production
        if ($this->isDomainIdentity($identity)) {
            return true;
        }

        return false === $this->isTestEmail($identity);
    }

    /**
     * @param string $mailbox
     *
     * @return bool
     *
     * @internal
     */
    public function isTestEmail(string $mailbox): bool
    {
        if ($this->isEmailIdentity($mailbox)) {
            $mailbox = $this->getEmailParts($mailbox)['mailbox'];
        }

        return (bool) strstr($mailbox, self::TEST_MAILBOX);
    }
}
