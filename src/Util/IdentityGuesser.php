<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Util;

use function Safe\sprintf;

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
     * @internal
     */
    public function getEmailParts(string $identity): array
    {
        // Here we explicitly check for email as this way it intercepts also wrong
        // $identities like, for example, "an_identity" that is not an email not a domain
        if (false === $this->isEmailIdentity($identity)) {
            throw new \InvalidArgumentException(sprintf('The value "%s" is not an Email identity.', $identity));
        }

        $parts = \explode('@', $identity);

        return [
            self::MAILBOX => $parts[0],
            self::DOMAIN  => $parts[1],
        ];
    }

    public function isDomainIdentity(string $identity): bool
    {
        return false === $this->isEmailIdentity($identity);
    }

    /**
     * @internal
     */
    public function isEmailIdentity(string $identity): bool
    {
        return (bool) \strstr($identity, '@');
    }

    /**
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
