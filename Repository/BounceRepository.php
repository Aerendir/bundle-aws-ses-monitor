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
 *
 * {@inheritdoc}
 */
class BounceRepository extends EntityRepository implements BounceRepositoryInterface
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

    /**
     * @param Bounce $bounce
     *
     * @return mixed
     */
    public function save(Bounce $bounce)
    {
        $this->_em->persist($bounce);
        $this->_em->flush();
    }
}
