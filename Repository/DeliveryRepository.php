<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\DeliveryRepositoryInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Delivery;

/**
 * {@inheritdoc}
 */
class DeliveryRepository extends EntityRepository implements DeliveryRepositoryInterface
{
    /**
     * @param $email
     *
     * @return object|Delivery|null
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(['emailAddress' => mb_strtolower($email)]);
    }

    /**
     * @param Delivery $delivery
     *
     * @return mixed
     */
    public function save(Delivery $delivery)
    {
        $this->_em->persist($delivery);
        $this->_em->flush();
    }
}
