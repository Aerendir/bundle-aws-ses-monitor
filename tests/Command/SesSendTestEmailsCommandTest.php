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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Command\SesSendTestEmailsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

final class SesSendTestEmailsCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $sentMessages = [];
        $transport    = $this->createMock(TransportInterface::class);
        $transport
            ->expects(self::exactly(5))
            ->method('send')
            ->willReturnCallback(static function (RawMessage $message, ?Envelope $envelope = null) use (&$sentMessages): ?SentMessage {
                self::assertInstanceOf(Email::class, $message);
                $sentMessages[] = $message;
                $to             = $message->getTo()[0]->getAddress();

                return match ($to) {
                    // Rejected, as the MonitorFilterListener does when all the recipients are blacklisted
                    'complaint@simulator.amazonses.com'       => null,
                    'suppressionlist@simulator.amazonses.com' => throw new TransportException('The transport failed.'),
                    default                                   => new SentMessage($message, $envelope ?? Envelope::create($message)),
                };
            });

        $output = $this->executeCommand($transport, 'aws:ses:monitor:test:mailer');

        self::assertStringContainsString('Sending an email from test@example.com to success@simulator.amazonses.com', $output);
        self::assertStringContainsString('Sending an email from test@example.com to bounce@simulator.amazonses.com', $output);
        self::assertStringContainsString('Sending an email from test@example.com to ooto@simulator.amazonses.com', $output);
        self::assertStringContainsString('Sending an email from test@example.com to complaint@simulator.amazonses.com', $output);
        self::assertStringContainsString('Sending an email from test@example.com to suppressionlist@simulator.amazonses.com', $output);
        self::assertStringContainsString('The transport failed.', $output);
        self::assertStringContainsString('Email to success@simulator.amazonses.com sent.', $output);
        self::assertStringContainsString('Email to bounce@simulator.amazonses.com sent.', $output);
        self::assertStringContainsString('Email to ooto@simulator.amazonses.com sent.', $output);
        self::assertStringContainsString('Email to complaint@simulator.amazonses.com NOT sent.', $output);
        self::assertStringContainsString('Email to suppressionlist@simulator.amazonses.com NOT sent.', $output);

        self::assertCount(5, $sentMessages);
        foreach ($sentMessages as $message) {
            self::assertSame('test@example.com', $message->getFrom()[0]->getAddress());
            self::assertSame('Test message from Aws Ses Monitor Bundle', $message->getSubject());
            self::assertSame('This is a test message sent from the Aws SES Monitor Bundle command.', $message->getHtmlBody());
            self::assertSame('UTF-8', $message->getHtmlCharset());
        }
    }

    public function testTheOldNameIsAnAlias(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects(self::exactly(5))->method('send')->willReturn(null);

        $output = $this->executeCommand($transport, 'aws:ses:monitor:test:swiftmailer');

        self::assertStringContainsString('Email to success@simulator.amazonses.com NOT sent.', $output);
    }

    private function executeCommand(TransportInterface $transport, string $name): string
    {
        $application = new Application();
        $command     = new SesSendTestEmailsCommand($transport);

        // Application::add() is deprecated since Symfony 7.4 in favour of addCommand()
        \method_exists($application, 'addCommand') ? $application->addCommand($command) : $application->add($command);

        /** @var Command $command */
        $command = $application->find($name);
        self::assertSame('aws:ses:monitor:test:mailer', $command->getName());

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['test@example.com']);
        $commandTester->execute(['command' => $command->getName()]);

        // the output of the command in the console, without the formatting
        return \strip_tags($commandTester->getDisplay());
    }
}
