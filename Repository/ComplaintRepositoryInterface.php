<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * (c) Adamo Aerendir Crespi.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;

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
