<?php
namespace Shivas\BouncerBundle\Model;

class Topic
{
    /** @var integer */
    protected $id;

    /** @var string */
    protected $topicArn;

    /** @var null|string */
    protected $token;

    public function __construct($topicArn, $token = null)
    {
        $this->topicArn = $topicArn;
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null|string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param string $topicArn
     * @return $this
     */
    public function setTopicArn($topicArn)
    {
        $this->topicArn = $topicArn;
        return $this;
    }

    /**
     * @return string
     */
    public function getTopicArn()
    {
        return $this->topicArn;
    }
}
