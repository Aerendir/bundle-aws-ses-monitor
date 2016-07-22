<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\TopicRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * {@inheritdoc}
 */
class SnsSetupBouncesTopicCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription(
            'Registers SNS Topic, attaches it to chosen identities as bounce topic and subscribes endpoint to receive bounce notifications'
        );
        $this->setName('swiftmailer:sns:setup-bounce-topic');
        $this->addArgument('name', InputArgument::REQUIRED, 'Topic name to create, follows AWS naming rules');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var RequestContext $context */
        $context = $container->get('router')->getContext();
        $context->setHost($container->getParameter('aws_ses_monitor.bounces_endpoint')['host']);
        $context->setScheme($container->getParameter('aws_ses_monitor.bounces_endpoint')['protocol']);

        $apiFactory = $container->get('aws_ses_monitor.aws.client.factory');

        $credentials = $container->getParameter('aws_ses_monitor.aws_config')['credentials_service_name'];
        $sesClient = $apiFactory->getSesClient($container->get($credentials));
        $snsClient = $apiFactory->getSnsClient($container->get($credentials));

        // fetch identities
        $response = $sesClient->listIdentities();
        $identities = $response->get('Identities');
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select identities to hook to: (comma separated numbers, default: all)',
            $identities,
            implode(",", range(0, count($identities) - 1, 1))
        );

        $question->setMultiselect(true);
        $selectedIdentities = $helper->ask($input, $output, $question);

        // create SNS topic
        $topic = array('Name' => $input->getArgument('name'));
        $response = $snsClient->createTopic($topic);
        $topicArn = $response->get('TopicArn');

        /** @var TopicRepositoryInterface $topicRepo */
        $topicRepo = $container->get('aws_ses_monitor.object_manager')->getRepository('AwsSesMonitorBundle:Topic');
        $topicRepo->save(new Topic($topicArn));

        $output->writeln("\nTopic created: " . $topicArn . "\n");

        // subscribe selected SES identities to SNS topic
        $output->writeln(sprintf('Registering <comment>"%s"</comment> topic for identities:', $topic['Name']));
        foreach ($selectedIdentities as $identity) {
            $output->write($identity . ' ... ');
            $sesClient->setIdentityNotificationTopic(
                array(
                    'Identity' => $identity,
                    'NotificationType' => 'Bounce',
                    'SnsTopic' => $topicArn
                )
            );
            $output->writeln('OK');
        }

        $subscribe = [
            'TopicArn' => $topicArn,
            'Protocol' => $container->getParameter('aws_ses_monitor.bounces_endpoint')['protocol'],
            'Endpoint' => $this->getContainer()
                ->get('router')
                ->generate(
                    $container->getParameter(
                        'aws_ses_monitor.bounces_endpoint'
                    )['route_name'],
                    array(),
                    RouterInterface::ABSOLUTE_URL
                )
        ];

        $response = $snsClient->subscribe($subscribe);

        $output->writeln(sprintf("\nSubscription endpoint URI: <comment>%s</comment>\n", $subscribe['Endpoint']));
        $output->writeln(sprintf("Subscription status: <comment>%s</comment>", $response->get('SubscriptionArn')));
    }
}
