<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Mail;
use Doctrine\ORM\EntityRepository;

/**
 * {@inheritdoc}
 */
class MailRepository extends EntityRepository implements MailRepositoryInterface
{
    /**
     * @param string $messageId
     *
     * @return Mail|null|object
     */
    public function findOneByMessageId($messageId)
    {
        return $this->findOneBy(['messageId' => $messageId]);
    }
}
