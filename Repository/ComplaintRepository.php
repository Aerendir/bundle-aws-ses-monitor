<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Complaint;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\ComplaintRepositoryInterface;

/**
 * {@inheritdoc}
 */
class ComplaintRepository extends EntityRepository implements ComplaintRepositoryInterface
{
    /**
     * @param $email
     * @return object|Complaint|null
     */
    public function findComplaintByEmail($email)
    {
        return $this->findOneBy(['emailAddress' => mb_strtolower($email)]);
    }

    /**
     * @param Complaint $bounce
     * @return mixed
     */
    public function save(Complaint $bounce)
    {
        $this->_em->persist($bounce);
        $this->_em->flush();
    }
}
