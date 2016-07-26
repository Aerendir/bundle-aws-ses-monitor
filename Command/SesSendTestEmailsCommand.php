<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Sends test emails to the addresses provided by AWS SES.
 *
 * @see: http://docs.aws.amazon.com/ses/latest/DeveloperGuide/mailbox-simulator.html
 *
 * {@inheritdoc}
 */
class SesSendTestEmailsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription(
            'Sends test emails through SwiftMailer to the addresses provided by AWS SES.'
        );
        $this->setName('aws:ses:monitor:test:swiftmailer');
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
        $emailAddresses = [
            'success@simulator.amazonses.com',
            'bounce@simulator.amazonses.com',
            'ooto@simulator.amazonses.com',
            'complaint@simulator.amazonses.com',
            'suppressionlist@simulator.amazonses.com'
        ];

        $question = new Question('Please enter the from email address to use:');

        $fromAddress = $this->getHelper('question')->ask($input, $output, $question);

        foreach ($emailAddresses as $toAddress) {
            $message = $this->createMessage($fromAddress, $toAddress);
            $output->writeln(sprintf('Sending an email from <comment>%s</comment> to <comment>%s</comment>', $fromAddress, $toAddress));
            $this->getContainer()->get('mailer')->send($message);
        }
    }

    /**
     * @param $sendTo
     *
     * @return \Swift_Mime_SimpleMimeEntity
     */
    private function createMessage($sendFrom, $sendTo)
    {
        return \Swift_Message::newInstance()
            ->setSubject('Test message from Aws Ses Monitor Bundle')
            ->setFrom($sendFrom)
            ->setTo($sendTo)
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody('This is a test message sent from the Aws SES Monitor Bundle command.');
    }
}
