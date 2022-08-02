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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;

final class EmailStatusManagerTest extends TestCase
{
    /**
     * ! ! ! Upper and lower case letters ! ! !
     *
     * @var string
     */
    private const TEST_EMAIL = 'Test@serEndipItyhq.com';

    public function testCreateEmailStatus(): void
    {
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects(self::exactly(1))->method('persist');

        $emailStatusManager = new EmailStatusManager($mockEntityManager);
        $result             = $emailStatusManager->createEmailStatus('test@serendipityhq.com');

        self::assertInstanceOf(EmailStatus::class, $result);
    }

    public function testLoadEmailStatus(): void
    {
        $mockEmailStatusRepository = $this->createMock(EntityRepository::class);
        $mockEmailStatusRepository
            ->expects(self::exactly(1))
            ->method('findOneBy')
            // ! ! ! All letters are lowercased ! ! !
            ->with(self::equalTo(['address' => 'test@serendipityhq.com']));
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects(self::exactly(1))->method('getRepository')->willReturn($mockEmailStatusRepository);
        $emailStatusManager = new EmailStatusManager($mockEntityManager);
        $emailStatusManager->loadEmailStatus(self::TEST_EMAIL);
    }

    public function testLoadOrCreateEmaillStatus(): void
    {
        $mockEmailStatusRepository = $this->createMock(EntityRepository::class);
        $mockEmailStatusRepository
            ->method('findOneBy')
            ->willReturn(null);
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects(self::exactly(1))->method('getRepository')->willReturn($mockEmailStatusRepository);

        $emailStatusManager = new EmailStatusManager($mockEntityManager);
        $result             = $emailStatusManager->loadOrCreateEmailStatus('test@serendipityhq.com');

        self::assertInstanceOf(EmailStatus::class, $result);
    }
}
