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

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;

/**
 * Repository to manage a Topic.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */
interface TopicRepositoryInterface
{
    /**
     * @param $topicArn
     *
     * @return Topic|null
     */
    public function findOneByTopicArn($topicArn);
}
