<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Model;

use Aws\Sns\MessageValidator\Message;
use Aws\Sns\MessageValidator\MessageValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\AwsClientFactory;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionConfirmationHandler implements BouncerHandlerInterface
{
    const HEADER_TYPE = 'SubscriptionConfirmation';

    /**
     * @var TopicRepositoryInterface
     */
    private $repo;
    /**
     * @var AwsClientFactory
     */
    private $clientFactory;

    /**
     * @param ObjectRepository $repo
     * @param AwsClientFactory $clientFactory
     */
    public function __construct(ObjectRepository $repo, AwsClientFactory $clientFactory)
    {
        $this->repo = $repo;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Request $request
     * @return int
     */
    public function handleRequest(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return 405;
        }

        try {
            $data = json_decode($request->getContent(), true);
            $message = Message::fromArray($data);
            $validator = new MessageValidator();
            $validator->isValid($message);
        } catch (\Exception $e) {
            return 404;
        }

        if (isset($data['Token']) && isset($data['TopicArn'])) {
            $topicArn = $data['TopicArn'];
            $token = $data['Token'];

            $topicEntity = $this->repo->getTopicByArn($topicArn);
            if ($topicEntity instanceof Topic) {
                $topicEntity->setToken($token);
                $this->repo->save($topicEntity);

                $client = $this->clientFactory->getSnsClient();
                $client->confirmSubscription(
                    array(
                        'TopicArn' => $topicEntity->getTopicArn(),
                        'Token' => $topicEntity->getToken()
                    )
                );

                $this->repo->remove($topicEntity);
                return 200;
            }
        }
        return 404;
    }
}
