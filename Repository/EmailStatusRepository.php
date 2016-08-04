<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class EmailStatusRepository extends EntityRepository implements ByEmailAddressInterface
{
    /**
     * @param $email
     *
     * @return object|Delivery|null
     */
    public function findOneByEmailAddress($email)
    {
        return $this->findOneBy(['emailAddress' => mb_strtolower($email)]);
    }
}
