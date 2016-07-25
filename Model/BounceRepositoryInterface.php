<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Repository to manage Bounces.
 */
interface BounceRepositoryInterface
{
    /**
     * @param $email
     *
     * @return Bounce|null
     */
    public function findOneByEmail($email);

    /**
     * @param Bounce $bounce
     *
     * @return mixed
     */
    public function save(Bounce $bounce);
}
