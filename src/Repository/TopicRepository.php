<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
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
     * @return object|Topic|null
     */
    public function findOneByTopicArn($topicArn)
    {
        return $this->findOneBy(['topicArn' => $topicArn]);
    }
}
