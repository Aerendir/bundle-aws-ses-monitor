<?php
namespace Shivas\BouncerBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;

/**
 * Creates clients for SES and SNS.
 * @see https://aws.amazon.com/it/documentation/ses/
 * @see https://aws.amazon.com/it/documentation/sns/
 */
class AwsClientFactory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param Credentials $credentials
     * @return SesClient
     */
    public function getSesClient(Credentials $credentials)
    {
        $config = $this->buildSesConfig($credentials);
        return new SesClient($config);
    }

    /**
     * @param Credentials $credentials
     * @return SnsClient
     */
    public function getSnsClient(Credentials $credentials)
    {
        $config = $this->buildSnsConfig($credentials);
        return new SnsClient($config);
    }

    /**
     * @param Credentials $credentials
     * @return array
     */
    private function buildSesConfig(Credentials $credentials)
    {
        return array(
            'credentials' => $credentials,
            'region'      => $this->config['region'],
            'version'     => $this->config['ses_version']
        );
    }

    /**
     * @param Credentials $credentials
     * @return array
     */
    private function buildSnsConfig(Credentials $credentials)
    {
        return array(
            'credentials' => $credentials,
            'region'      => $this->config['region'],
            'version'     => $this->config['sns_version']
        );
    }
}
