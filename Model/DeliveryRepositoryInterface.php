<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

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
