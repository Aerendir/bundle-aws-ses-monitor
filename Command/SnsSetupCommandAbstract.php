<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\TopicRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * {@inheritdoc}
 */
abstract class SnsSetupCommandAbstract extends ContainerAwareCommand
{
    /** @var string $endpoint */
    private $endpoint;

    /** @var SesClient $sesClient */
    private $sesClient;

    /** @var SnsClient $snsClient */
    private $snsClient;

    /** @var string $topicArn */
    private $topicArn;

    /**
     * @return string
     */
    abstract public function getTopicName();

    /**
     * @return string
     */
    abstract public function getTopicKind();

    /**
     * Executes the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int|bool null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Make the common configurations
        $this->configureCommand($this->getTopicName());

        // Show to developer the selction of identities
        $selectedIdentities = $this->getHelper('question')->ask($input, $output, $this->createIdentitiesQuestion());

        // Create and persist the topic
        $topicArn = $this->createSnsTopic($this->getTopicName(), $output);

        if (false === $topicArn) {
            return false;
        }

        $output->writeln("\nTopic created: " . $topicArn . "\n");

        // subscribe selected SES identities to SNS topic
        $output->writeln(sprintf('Registering <comment>"%s"</comment> topic for identities:', $this->getContainer()->getParameter($this->getTopicName())['topic']['name']));
        foreach ($selectedIdentities as $identity) {
            $output->write($identity . ' ... ');
            $this->setIdentityInSesClient($identity, $this->getTopicKind());
            $output->writeln('OK');
        }

        $subscribe = $this->buildSubscribeArray();
        $response = $this->getSnsClient()->subscribe($subscribe);

        $output->writeln(sprintf("\nSubscription endpoint URI: <comment>%s</comment>\n", $subscribe['Endpoint']));
        $output->writeln(sprintf('Subscription status: <comment>%s</comment>', $response->get('SubscriptionArn')));

        return true;
    }

    /**
     * Performs common tasks for setup commands.
     *
     * @param string $topicName The kind of email handling (bounces, complaints, ecc.)
     */
    public function configureCommand($topicName)
    {
        $this->endpoint = $topicName;

        /** @var RequestContext $context */
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost($this->getContainer()->getParameter($topicName)['topic']['endpoint']['host']);
        $context->setScheme($this->getContainer()->getParameter($topicName)['topic']['endpoint']['protocol']);

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
            implode(',', range(0, count($identities) - 1, 1))
        );
        $question->setMultiselect(true);

        return $question;
    }

    /**
     * Creates and persists a topic.
     *
     * @param string          $topicName The kind of email handling (bounces, complaints, ecc.)
     * @param OutputInterface $output
     *
     * @return string The created topic's ARN
     */
    public function createSnsTopic($topicName, OutputInterface $output)
    {
        $name = $this->getContainer()->getParameter($topicName)['topic']['name'];

        if ('not_set' === $name) {
            $output->writeln('<error>You have to set a name for the creating topic. Specify it in aws_ses_monitor.[bounces|complaints].topic_name.</error>');

            return false;
        }

        // create SNS topic
        $topic = ['Name' => $name];
        $response = $this->getSnsClient()->createTopic($topic);
        $this->topicArn = $response->get('TopicArn');

        /** @var TopicRepositoryInterface $topicRepo */
        $topicRepo = $this->getContainer()->get('aws_ses_monitor.entity_manager')->getRepository('AwsSesMonitorBundle:Topic');
        $topicRepo->save(new Topic($this->topicArn));

        return $this->topicArn;
    }

    /**
     * Sets the chosen identity in the SesClient.
     *
     * @param string $identity
     * @param string $type     The type of notification
     *
     *                     @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitynotificationtopic
     */
    public function setIdentityInSesClient($identity, $type)
    {
        $this->getSesClient()->setIdentityNotificationTopic(
            [
                'Identity' => $identity,
                'NotificationType' => $type,
                'SnsTopic' => $this->topicArn
            ]
        );
    }

    /**
     * @return array
     */
    public function buildSubscribeArray()
    {
        return [
            'TopicArn' => $this->topicArn,
            'Protocol' => $this->getContainer()->getParameter($this->endpoint)['topic']['endpoint']['protocol'],
            'Endpoint' => $this->getContainer()
                ->get('router')
                ->generate(
                    $this->getContainer()->getParameter($this->endpoint)['topic']['endpoint']['route_name'],
                    [],
                    RouterInterface::ABSOLUTE_URL
                )
        ];
    }
}
