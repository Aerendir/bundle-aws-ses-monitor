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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Email;

/**
 * Manages Email entities.
 */
class EmailManager
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
     * @return Email
     */
    public function createEmail(string $emailAddress): Email
    {
        $email = new Email($emailAddress);
        $this->entityManager->persist($email);

        return $email;
    }

    /**
     * @param string $emailAddress
     *
     * @return Email|null
     */
    public function loadEmail(string $emailAddress): ?Email
    {
        /** @var Email|null $email */
        $email = $this->entityManager->getRepository(Email::class)->findOneBy(['address' => mb_strtolower($emailAddress)]);

        return $email;
    }

    /**
     * @param string $emailAddress
     *
     * @return Email
     */
    public function loadOrCreateEmail(string $emailAddress): Email
    {
        $email = $this->loadEmail($emailAddress);

        if (null === $email) {
            $email = $this->createEmail($emailAddress);
        }

        return $email;
    }
}
