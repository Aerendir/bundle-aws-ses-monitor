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

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\AwsDataProcessor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Service\Monitor;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 *
 * @codeCoverageIgnore This command basically calls AWS and uses other classes already tested, so it is not testable.
 */
class DebugCommand extends Command
{
    public const NAME   = 'aws:ses:debug';
    private const THICK = "<fg=green>\xE2\x9C\x94</>";
    private const CROSS = "<fg=red>\xE2\x9C\x96</>";

    /** @var Console $console */
    private $console;

    /** @var Monitor $monitor */
    private $monitor;

    private $sectionTitle;

    private $sectionBody;

    /**
     * @param Console $console
     * @param Monitor $monitor
     */
    public function __construct(Console $console, Monitor $monitor)
    {
        $this->console = $console;
        $this->monitor = $monitor;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Debugs the aws ses configuration helping discovering errors and wrong settings.')
             ->setName(self::NAME)
             ->addOption('full-log', null, InputOption::VALUE_NONE, 'Shows logs line by line, without simply changing the current one.');
    }

    /**
     * {@inheritdoc}
     *
     * @param ConsoleOutput&OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->console->enableFullLog((bool) $input->getOption('full-log'));

        // Create the Input/Output writer
        $ioWriter = $this->console->createWriter($input, $output);

        $ioWriter->title('Debug Aws SES and SNS');

        $this->sectionTitle = $this->console->createSection($output);
        $this->sectionBody  = $this->console->createSection($output);
        $this->monitor->retrieve($this->sectionTitle, $this->sectionBody, true);

        $validationResults                               = [];
        $validationResults[AwsDataProcessor::ACCOUNT]    = $this->validateAccountData();
        $validationResults[AwsDataProcessor::IDENTITIES] = $this->validateIdentitiesData();

        $this->console->overwrite('Preparing data', $this->sectionTitle);
        $table = $this->showData($validationResults);

        // Finally render the table with results
        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);
        $table->render();
    }

    /**
     * @return array
     */
    private function validateAccountData(): array
    {
        $results = [];

        $this->console->overwrite('Validating Account', $this->sectionTitle);

        // Can the account send emails?
        $results[] = ['   Account enabled for sending', $this->monitor->getAccount('enabled') ? self::THICK : self::CROSS];

        // Is the quota exceeded?
        $results[] = [
            '   Quota still available',
            $this->monitor->getAccount('quota')['sent_last_24_hours'] <= $this->monitor->getAccount('quota')['max_24_hour_send']
                ? self::THICK
                : self::CROSS,
        ];

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);

        return $results;
    }

    /**
     * @return array
     */
    private function validateIdentitiesData(): array
    {
        $this->console->overwrite('Validating Identities', $this->sectionTitle);
        $results = [];

        foreach ($this->monitor->getConfiguredIdentitiesList(true)['allowed'] as $identity) {
            $this->console->overwrite(sprintf('Validating identity <comment>%s</comment>', $identity), $this->sectionBody);

            // Does the identity exists on AWS?
            $results[$identity][] = ['   Created on AWS', $this->monitor->liveIdentityExists($identity) ? self::THICK : self::CROSS];
            if (false === $this->monitor->liveIdentityExists($identity)) {
                // If it is not still created on AWS, simply skip the rest of checks
                continue;
            }

            // Is identity verified?
            $results[$identity][] = ['   Verified (enabled for sending)', $this->monitor->liveIdentityIsVerified($identity) ? self::THICK : self::CROSS];

            // Is DKIM enabled?
            $results[$identity][] = ['   DKIM enabled', $this->monitor->liveIdentityDkimIsEnabled($identity) ? self::THICK : self::CROSS];

            // Is DKIM verified?
            $results[$identity][] = ['   DKIM verified', $this->monitor->liveIdentityDkimIsVerified($identity) ? self::THICK : self::CROSS];

            // Notifications include headers?
            $results[$identity][] = ['   Bounces notifications include headers', $this->monitor->liveNotificationsIncludeHeaders($identity, 'bounces') ? self::THICK : self::CROSS];
            $results[$identity][] = ['   Complaints notifications include headers', $this->monitor->liveNotificationsIncludeHeaders($identity, 'complaints') ? self::THICK : self::CROSS];
            $results[$identity][] = ['   Deliveries notifications include headers', $this->monitor->liveNotificationsIncludeHeaders($identity, 'deliveries') ? self::THICK : self::CROSS];
        }

        $this->console->clear($this->sectionBody);
        $this->console->clear($this->sectionTitle);

        return $results;
    }

    /**
     * @param array $validationResults
     *
     * @return Table
     */
    private function showData(array $validationResults): Table
    {
        $table = new Table($this->sectionBody);
        $table->setHeaders([
            [new TableCell('Results', ['colspan' => 2])],
        ]);

        $this->console->overwrite('Processing Account results', $this->sectionBody);
        $table->addRow([new TableCell('<success>ACCOUNT</success>', ['colspan' => 2])]);
        foreach ($validationResults[AwsDataProcessor::ACCOUNT] as $result) {
            $table->addRow($result);
        }

        $this->console->overwrite('Processing Identities results', $this->sectionBody);
        $table->addRow([new TableCell('<success>IDENTITIES</success>', ['colspan' => 2])]);
        foreach ($validationResults[AwsDataProcessor::IDENTITIES] as $identity => $results) {
            $table->addRow([new TableCell($identity, ['colspan' => 2])]);
            foreach ($results as $result) {
                $table->addRow($result);
            }
        }

        return $table;
    }
}
