<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Command\SesSendTestEmailsCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

/**
 * {@inheritdoc}
 */
class SesSendTestEmailsCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new SesSendTestEmailsCommand());

        /** @var ContainerAwareCommand $command */
        $command = $application->find('aws:ses:monitor:test:swiftmailer');
        $command->setContainer($this->getMockContainer());

        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("test@example.com\n"));

        $commandTester->execute(['command'  => $command->getName()]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        self::assertContains('Sending an email from test@example.com to success@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to bounce@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to ooto@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to complaint@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to suppressionlist@simulator.amazonses.com', $output);
    }

    /**
     * @param $input
     *
     * @return resource
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockContainer()
    {
        // Mock the container and everything you'll need here
        $mockSwiftMailer = $this->createMock(\Swift_Mailer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->expects(self::exactly(5))
            ->method('get')
            ->with('mailer')
            ->willReturn($mockSwiftMailer);

        return $mockContainer;
    }
}
