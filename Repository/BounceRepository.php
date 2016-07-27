<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Bounce;
use Doctrine\ORM\EntityRepository;

/**
 * {@inheritdoc}
 */
class BounceRepository extends EntityRepository implements BounceRepositoryInterface
{
    /**
     * @param $email
     *
     * @return object|Bounce|null
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(['email' => mb_strtolower($email)]);
    }

    /**
     * @param Bounce $bounce
     *
     * @return mixed
     */
    public function save(Bounce $bounce)
    {
        $this->_em->persist($bounce);
        $this->_em->flush();
    }
}
