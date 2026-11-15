<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

/**
 * Sends test emails to the addresses provided by AWS SES.
 *
 * The emails are sent through the default Mailer transport, synchronously, so the command can tell which ones were
 * not sent: those rejected by the MonitorFilterListener, because all their recipients are bounced or complained,
 * and those the transport failed to send.
 *
 * @see: http://docs.aws.amazon.com/ses/latest/DeveloperGuide/mailbox-simulator.html
 */
#[AsCommand(
    name: 'aws:ses:monitor:test:mailer',
    description: 'Sends test emails through Symfony Mailer to the addresses provided by AWS SES.',
    aliases: ['aws:ses:monitor:test:swiftmailer'],
)]
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

    private TransportInterface $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;

        parent::__construct();
    }

    protected function configure(): void
    {
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

            try {
                $sent = null !== $this->transport->send($message);
            } catch (TransportExceptionInterface $transportException) {
                $output->writeln(sprintf('<error>%s</error>', $transportException->getMessage()));
                $sent = false;
            }

            $tag           = 'fg=green';
            $outputMessage = 'Email to ' . $toAddress . ' ';

            if (false === $sent) {
                $tag = 'fg=red;';
                $outputMessage .= '<options=bold>NOT</> ';
            }

            $outputMessage .= 'sent.';

            $sents[] = '<' . $tag . '>' . $outputMessage . '</>';
        }

        foreach ($sents as $sent) {
            $output->writeln($sent);
        }

        return (int) Command::SUCCESS;
    }

    private function createMessage(string $sendFrom, string $sendTo): Email
    {
        return (new Email())
            ->subject('Test message from Aws Ses Monitor Bundle')
            ->from($sendFrom)
            ->to($sendTo)
            ->html('This is a test message sent from the Aws SES Monitor Bundle command.', 'UTF-8');
    }
}
