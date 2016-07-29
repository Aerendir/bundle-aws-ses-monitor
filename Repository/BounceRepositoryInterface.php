<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;

/**
 * Repository to manage Bounces.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
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
