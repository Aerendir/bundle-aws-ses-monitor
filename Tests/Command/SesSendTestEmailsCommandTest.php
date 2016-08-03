<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Command;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Command\SesSendTestEmailsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

/**
 * {@inheritdoc}
 */
class SesSendTestEmailsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new SesSendTestEmailsCommand());

        $command = $application->find('aws:ses:monitor:test:swiftmailer');
        $command->setContainer($this->getMockContainer());

        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("test@example.com\n"));

        $commandTester->execute(['command'  => $command->getName()]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Sending an email from test@example.com to success@simulator.amazonses.com', $output);
        $this->assertContains('Sending an email from test@example.com to bounce@simulator.amazonses.com', $output);
        $this->assertContains('Sending an email from test@example.com to ooto@simulator.amazonses.com', $output);
        $this->assertContains('Sending an email from test@example.com to complaint@simulator.amazonses.com', $output);
        $this->assertContains('Sending an email from test@example.com to suppressionlist@simulator.amazonses.com', $output);
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
        $mockContainer->expects($this->exactly(5))
            ->method('get')
            ->with('mailer')
            ->willReturn($mockSwiftMailer);

        return $mockContainer;
    }
}
