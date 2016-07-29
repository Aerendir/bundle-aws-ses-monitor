<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;

/**
 * {@inheritdoc}
 */
class ComplaintRepository extends EntityRepository implements ComplaintRepositoryInterface
{
    /**
     * @param $email
     *
     * @return object|Complaint|null
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(['emailAddress' => mb_strtolower($email)]);
    }

    /**
     * @param Complaint $complaint
     *
     * @return mixed
     */
    public function save(Complaint $complaint)
    {
        $this->_em->persist($complaint);
        $this->_em->flush();
    }
}
