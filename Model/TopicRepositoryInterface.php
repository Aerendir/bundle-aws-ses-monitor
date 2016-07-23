<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

interface TopicRepositoryInterface
{
    /**
     * @param $topicArn
     *
     * @return Topic|null
     */
    public function getTopicByArn($topicArn);

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
