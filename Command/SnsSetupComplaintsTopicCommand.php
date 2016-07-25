<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class SnsSetupComplaintsTopicCommand extends SnsSetupCommandAbstract
{
    const KIND = 'aws_ses_monitor.complaints';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription(
            'Registers SNS Topic, attaches it to chosen identities as bounce topic and subscribes endpoint to receive bounce notifications'
        );
        $this->setName('aws:ses:monitor:setup:complaints-topic');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Make the common configurations
        $this->configureCommand(self::KIND);

        // Show to developer the selction of identities
        $selectedIdentities = $this->getHelper('question')->ask($input, $output, $this->createIdentitiesQuestion());

        // Create and persist the topic
        $topicArn = $this->createSnsTopic(self::KIND, $output);

        if (false === $topicArn)
            return false;

        $output->writeln("\nTopic created: " . $topicArn . "\n");

        // subscribe selected SES identities to SNS topic
        $output->writeln(sprintf('Registering <comment>"%s"</comment> topic for identities:', $this->getContainer()->getParameter(self::KIND)['topic']['name']));
        foreach ($selectedIdentities as $identity) {
            $output->write($identity . ' ... ');
            $this->setIdentityInSesClient($identity, 'Complaint');
            $output->writeln('OK');
        }

        $subscribe = $this->buildSubscribeArray();
        $response = $this->getSnsClient()->subscribe($subscribe);

        $output->writeln(sprintf("\nSubscription endpoint URI: <comment>%s</comment>\n", $subscribe['Endpoint']));
        $output->writeln(sprintf('Subscription status: <comment>%s</comment>', $response->get('SubscriptionArn')));
    }
}
