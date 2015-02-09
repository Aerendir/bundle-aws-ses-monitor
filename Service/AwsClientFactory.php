<?php
namespace Shivas\BouncerBundle\Service;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;

class AwsClientFactory
{
    /**
     * @var array
     */
    private $awsApiKeyConfig;

    /**
     * @param array $awsApiKeyConfig
     */
    public function __construct($awsApiKeyConfig)
    {
        $this->awsApiKeyConfig = $awsApiKeyConfig;
    }

    /**
     * @return SesClient
     */
    public function getSesClient()
    {
        return SesClient::factory($this->awsApiKeyConfig);
    }

    /**
     * @return SnsClient
     */
    public function getSnsClient()
    {
        return SnsClient::factory($this->awsApiKeyConfig);
    }
}
