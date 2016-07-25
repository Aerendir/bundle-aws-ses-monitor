<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

/**
 * Repository to manage a Topic.
 */
interface TopicRepositoryInterface
{
    /**
     * @param $topicArn
     *
     * @return Topic|null
     */
    public function findOneByTopicArn($topicArn);

    /**
     * @param Topic $topic
     *
     * @return mixed
     */
    public function save(Topic $topic);

    /**
     * @param Topic $topic
     *
     * @return mixed
     */
    public function remove(Topic $topic);
}
