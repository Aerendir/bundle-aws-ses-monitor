<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use function Safe\sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
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
final class SesSendTestEmailsCommand extends Command
{
    /** @var string[] */
    private const EMAIL_ADDRESSES = [
        'success@simulator.amazonses.com',
        'bounce@simulator.amazonses.com',
        'ooto@simulator.amazonses.com',
        'complaint@simulator.amazonses.com',
        'suppressionlist@simulator.amazonses.com',
    ];

    /** @var string */
    protected static $defaultName = 'aws:ses:monitor:test:swiftmailer';

    private \Swift_Mailer $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription(
            'Sends test emails through SwiftMailer to the addresses provided by AWS SES.'
        );
    }

    /**
     * Executes the command.
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $question = new Question('<question>Please, enter the from email address to use:</question>');
        $ask      = $this->getHelper('question');

        if (false === $ask instanceof QuestionHelper) {
            throw new \RuntimeException('The $ask object is not of type QuestionHelper');
        }

        $fromAddress = $ask->ask($input, $output, $question);
        $sents       = [];
        foreach (self::EMAIL_ADDRESSES as $toAddress) {
            $message = $this->createMessage($fromAddress, $toAddress);
            $output->writeln(sprintf('<info>Sending an email from <comment>%s</comment> to <comment>%s</comment></info>', $fromAddress, $toAddress));
            $result = $this->mailer->send($message);

            $tag           = 'fg=green';
            $outputMessage = 'Email to ' . $toAddress . ' ';

            if (0 === $result) {
                $tag = 'fg=red;';
                $outputMessage .= '<options=bold>NOT</> ';
            }

            $outputMessage .= 'sent.';

            $sents[] = '<' . $tag . '>' . $outputMessage . '</>';
        }

        foreach ($sents as $sent) {
            $output->writeln($sent);
        }

        return 0;
    }

    private function createMessage(string $sendFrom, string $sendTo): \Swift_Message
    {
        return (new \Swift_Message())
            ->setSubject('Test message from Aws Ses Monitor Bundle')
            ->setFrom($sendFrom)
            ->setTo($sendTo)
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody('This is a test message sent from the Aws SES Monitor Bundle command.');
    }
}
