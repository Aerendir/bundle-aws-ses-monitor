<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class BounceRepository extends EntityRepository implements ByEmailInterface
{
    /**
     * @param $email
     *
     * @return object|Bounce|null
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(['emailAddress' => mb_strtolower($email)]);
    }
}
