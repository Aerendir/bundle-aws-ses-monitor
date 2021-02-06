<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Util;

/**
 * Provides some static methods to better understand
 * the nature of a configured identity.
 *
 * @internal
 */
final class IdentityGuesser
{
    /** @var string The name of the testing email Identity */
    public const TEST_MAILBOX = 'test_aws';

    /** @var string */
    public const MAILBOX = 'mailbox';

    /** @var string */
    public const DOMAIN = 'domain';

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
            throw new \InvalidArgumentException(\Safe\sprintf('The value "%s" is not an Email identity.', $identity));
        }

        $parts = \explode('@', $identity);

        return [
            self::MAILBOX => $parts[0],
            self::DOMAIN  => $parts[1],
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
        return (bool) \strstr($identity, '@');
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

        return (bool) \strstr($mailbox, self::TEST_MAILBOX);
    }
}
