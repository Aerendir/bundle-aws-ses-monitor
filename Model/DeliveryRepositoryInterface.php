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
     * @param Complaint $bounce
     *
     * @return mixed
     */
    public function save(Complaint $bounce);
}
