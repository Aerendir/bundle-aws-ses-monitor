<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Repository to manage Complaints.
 */
interface ComplaintRepositoryInterface
{
    /**
     * @param $email
     * @return Bounce|null
     */
    public function findComplaintByEmail($email);

    /**
     * @param Complaint $bounce
     * @return mixed
     */
    public function save(Complaint $bounce);
}
