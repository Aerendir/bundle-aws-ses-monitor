<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;

/**
 * Repository to manage Complaints.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
interface DeliveryRepositoryInterface
{
    /**
     * @param $email
     *
     * @return Bounce|null
     */
    public function findOneByEmail($email);

    /**
     * @param Delivery $delivery
     *
     * @return mixed
     */
    public function save(Delivery $delivery);
}
