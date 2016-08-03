<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 *
 * {@inheritdoc}
 */
class TopicRepository extends EntityRepository implements TopicRepositoryInterface
{
    /**
     * @param $topicArn
     *
     * @return Topic|null|object
     */
    public function findOneByTopicArn($topicArn)
    {
        return $this->findOneBy(['topicArn' => $topicArn]);
    }
}
