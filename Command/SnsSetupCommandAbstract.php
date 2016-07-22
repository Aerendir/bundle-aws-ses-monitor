<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\TopicRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * {@inheritdoc}
 */
class SnsSetupCommandAbstract extends ContainerAwareCommand
{
    private $sesClient;
    private $snsClient;
    private $topicArn;

    /**
     * Performs common tasks for setup commands.
     *
     * @param string $endpoint
     */
    public function configureCommand($endpoint)
    {
        /** @var RequestContext $context */
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost($this->getContainer()->getParameter($endpoint)['host']);
        $context->setScheme($this->getContainer()->getParameter($endpoint)['protocol']);

        $apiFactory = $this->getContainer()->get('aws_ses_monitor.aws.client.factory');

        $credentials = $this->getContainer()->getParameter('aws_ses_monitor.aws_config')['credentials_service_name'];
        $this->sesClient = $apiFactory->getSesClient($this->getContainer()->get($credentials));
        $this->snsClient = $apiFactory->getSnsClient($this->getContainer()->get($credentials));
    }

    /**
     * @return SesClient
     */
    public function getSesClient()
    {
        return $this->sesClient;
    }

    /**
     * @return SnsClient
     */
    public function getSnsClient()
    {
        return $this->snsClient;
    }

    /**
     * Creates the questions to show to the developer during setup.
     *
     * The developer has to chose to which identity the created SNS has to be hooked.
     *
     * @return ChoiceQuestion
     */
    public function createIdentitiesQuestion()
    {
        $response = $this->getSesClient()->listIdentities();
        $identities = $response->get('Identities');
        $question = new ChoiceQuestion(
            'Please select identities to hook to: (comma separated numbers, default: all)',
            $identities,
            implode(",", range(0, count($identities) - 1, 1))
        );
        $question->setMultiselect(true);

        return $question;
    }

    /**
     * Creates and persists a topic.
     *
     * @param string $name
     *
     * @return string The created topic's ARN
     */
    public function createSnsTopic($name)
    {
        // create SNS topic
        $topic = ['Name' => $name];
        $response = $this->getSnsClient()->createTopic($topic);
        $this->topicArn = $response->get('TopicArn');

        /** @var TopicRepositoryInterface $topicRepo */
        $topicRepo = $this->getContainer()->get('aws_ses_monitor.object_manager')->getRepository('AwsSesMonitorBundle:Topic');
        $topicRepo->save(new Topic($this->topicArn));

        return $this->topicArn;
    }

    /**
     * Sets the chosen identity in the SesClient.
     *
     * @param mixed $identity
     */
    public function setIdentityInSesClient($identity)
    {
        $this->getSesClient()->setIdentityNotificationTopic(
            [
                'Identity' => $identity,
                'NotificationType' => 'Bounce',
                'SnsTopic' => $this->topicArn
            ]
        );
    }

    public function buildSubscribeArray()
    {
        return [
            'TopicArn' => $this->topicArn,
            'Protocol' => $this->getContainer()->getParameter('aws_ses_monitor.complaints_endpoint')['protocol'],
            'Endpoint' => $this->getContainer()
                ->get('router')
                ->generate(
                    $this->getContainer()->getParameter(
                        'aws_ses_monitor.complaints_endpoint'
                    )['route_name'],
                    [],
                    RouterInterface::ABSOLUTE_URL
                )
        ];
    }
}
