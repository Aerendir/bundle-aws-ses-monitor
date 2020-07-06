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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\IdentityGuesser;

/**
 * {@inheritdoc}
 */
class IdentityGuesserTest extends TestCase
{
    /** @var string $testIdentity */
    private $testIdentity;

    /** @var string $emailIdentity */
    private $emailIdentity;

    /** @var string $domainIdentity */
    private $domainIdentity;

    /** @var string $productionMailbox */
    private $productionMailbox;

    public function setUp():void
    {
        $this->productionMailbox = 'hello';
        $this->domainIdentity    = 'serendipityhq.com';
        $this->testIdentity      = sprintf('%s@s%s', IdentityGuesser::TEST_MAILBOX, $this->domainIdentity);
        $this->emailIdentity     = sprintf('%s@%s', $this->productionMailbox, $this->domainIdentity);
    }

    public function testGetEmailParts()
    {
        $resource = new IdentityGuesser();
        $result   = $resource->getEmailParts($this->emailIdentity);

        self::assertCount(2, $result);
        self::assertArrayHasKey('mailbox', $result);
        self::assertArrayHasKey('domain', $result);
        self::assertEquals($this->productionMailbox, $result['mailbox']);
        self::assertEquals($this->domainIdentity, $result['domain']);
    }

    public function testGetEmailPartsAcceptsOnlyEmailIdentities()
    {
        $resource = new IdentityGuesser();
        self::expectException(\InvalidArgumentException::class);
        $resource->getEmailParts($this->domainIdentity);
    }

    public function testIsEmailIdentity()
    {
        $resource = new IdentityGuesser();

        self::assertFalse($resource->isEmailIdentity($this->domainIdentity));
        self::assertTrue($resource->isEmailIdentity($this->emailIdentity));
    }

    public function testIsDomainIdentity()
    {
        $resource = new IdentityGuesser();

        self::assertTrue($resource->isDomainIdentity($this->domainIdentity));
        self::assertFalse($resource->isDomainIdentity($this->emailIdentity));
    }

    public function testIsTestEmail()
    {
        $resource = new IdentityGuesser();

        self::assertTrue($resource->isTestEmail($this->testIdentity));
        self::assertFalse($resource->isTestEmail($this->emailIdentity));
    }

    public function testIsProductionIdentity()
    {
        $resource = new IdentityGuesser();

        self::assertTrue($resource->isProductionIdentity($this->domainIdentity));
        self::assertTrue($resource->isProductionIdentity($this->emailIdentity));
        self::assertFalse($resource->isProductionIdentity($this->testIdentity));
    }
}
