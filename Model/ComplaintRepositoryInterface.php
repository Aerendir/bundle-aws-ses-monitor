<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Repository to manage Complaints.
 */
interface ComplaintRepositoryInterface
{
    /**
     * @param $email
     *
     * @return Complaint|null
     */
    public function findOneByEmail($email);

    /**
     * @param Complaint $complaint
     *
     * @return mixed
     */
    public function save(Complaint $complaint);
}
