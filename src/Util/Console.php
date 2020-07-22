<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Util;

use SerendipityHQ\Bundle\ConsoleStyles\Console\Formatter\SerendipityHQOutputFormatter;
use SerendipityHQ\Bundle\ConsoleStyles\Console\Style\SerendipityHQStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manages the compatibility between console 3 and 4.
 *
 * @internal
 */
class Console
{
    /** @var bool $fullLog If true, the sections are not used and lines are wrote one by one. */
    private $fullLog = false;

    /**
     * @param bool $enable
     */
    public function enableFullLog(bool $enable): void
    {
        $this->fullLog = $enable;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return SerendipityHQStyle
     *
     * @internal
     */
    public function createWriter(InputInterface $input, OutputInterface $output): SerendipityHQStyle
    {
        // Create the Input/Output writer
        $ioWriter = new SerendipityHQStyle($input, $output);
        $ioWriter->setFormatter(new SerendipityHQOutputFormatter(true));

        return $ioWriter;
    }

    /**
     * This method maintains compatibility between Console component 3 and 4.
     *
     * Once version 3 will not be supported anymore, anyway modify this method
     * accordingly to the linked issue, as there problems with static analysis.
     *
     * @see https://github.com/symfony/symfony/issues/27998
     *
     * @param ConsoleOutput|OutputInterface $output
     *
     * @return ConsoleSectionOutput|OutputInterface
     *
     * @internal
     */
    public function createSection($output)
    {
        return method_exists($output, 'section') && false === $this->fullLog
            ? $output->section()
            : $output;
    }

    /**
     * @param string                        $line
     * @param ConsoleOutput|OutputInterface $output
     *
     * @internal
     */
    public function overwrite(string $line, $output): void
    {
        method_exists($output, 'overwrite') && false === $this->fullLog
            ? $output->overwrite($line)
            : $output->writeln($line);
    }

    /**
     * @param ConsoleOutput|OutputInterface $output
     *
     * @internal
     */
    public function clear($output): void
    {
        if (method_exists($output, 'clear') && false === $this->fullLog) {
            $output->clear();
        }
    }
}
