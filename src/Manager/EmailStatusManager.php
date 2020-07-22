<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\EmailStatus;

/**
 * Manages EmailStatus entities.
 */
class EmailStatusManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $emailAddress
     *
     * @return EmailStatus
     */
    public function createEmailStatus(string $emailAddress): EmailStatus
    {
        $email = new EmailStatus($emailAddress);
        $this->entityManager->persist($email);

        return $email;
    }

    /**
     * @param string $emailAddress
     *
     * @return EmailStatus|null
     */
    public function loadEmailStatus(string $emailAddress): ?EmailStatus
    {
        /** @var EmailStatus|null $email */
        $email = $this->entityManager->getRepository(EmailStatus::class)->findOneBy(['address' => mb_strtolower($emailAddress)]);

        return $email;
    }

    /**
     * @param string $emailAddress
     *
     * @return EmailStatus
     */
    public function loadOrCreateEmailStatus(string $emailAddress): EmailStatus
    {
        $email = $this->loadEmailStatus($emailAddress);

        if (null === $email) {
            $email = $this->createEmailStatus($emailAddress);
        }

        return $email;
    }
}
