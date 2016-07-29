<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;

/**
 * Repository to manage Complaints.
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
