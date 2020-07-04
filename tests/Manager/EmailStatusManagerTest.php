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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager\EmailStatusManager;

/**
 * {@inheritdoc}
 */
class EmailStatusManagerTest extends TestCase
{
    public function testCreateEmailStatus()
    {
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects(self::exactly(1))->method('persist');

        $emailStatusManager = new EmailStatusManager($mockEntityManager);
        $result             = $emailStatusManager->createEmailStatus('test@serendipityhq.com');

        self::assertInstanceOf(EmailStatus::class, $result);
    }

    public function testLoadEmailStatus()
    {
        // ! ! ! Upper and lower case letters ! ! !
        $testEmail                 = 'Test@serEndipItyhq.com';
        $mockEmailStatusRepository = $this->createMock(EntityRepository::class);
        $mockEmailStatusRepository
            ->expects(self::exactly(1))
            ->method('findOneBy')
            // ! ! ! All letters are lowercased ! ! !
            ->with(self::equalTo(['address' => 'test@serendipityhq.com']));
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects(self::exactly(1))->method('getRepository')->willReturn($mockEmailStatusRepository);

        $emailStatusManager = new EmailStatusManager($mockEntityManager);
        $emailStatusManager->loadEmailStatus($testEmail);
    }

    public function testLoadOrCreateEmaillStatus()
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
