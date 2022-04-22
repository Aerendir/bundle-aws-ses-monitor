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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Manages the compatibility between console 3 and 4.
 *
 * @internal
 */
final class Console
{
    /** If true, the sections are not used and lines are wrote one by one. */
    private bool $fullLog = false;

    public function enableFullLog(bool $enable): void
    {
        $this->fullLog = $enable;
    }

    /**
     * @internal
     */
    public function createWriter(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        // Create the Input/Output writer
        $ioWriter = new SymfonyStyle($input, $output);

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
    public function createSection(OutputInterface $output): ConsoleSectionOutput
    {
        return \method_exists($output, 'section') && false === $this->fullLog
            ? $output->section()
            : $output;
    }

    /**
     * @internal
     */
    public function overwrite(string $line, OutputInterface $output): void
    {
        \method_exists($output, 'overwrite') && false === $this->fullLog
            ? $output->overwrite($line)
            : $output->writeln($line);
    }

    /**
     * @internal
     */
    public function clear(OutputInterface $output): void
    {
        if (\method_exists($output, 'clear') && false === $this->fullLog) {
            $output->clear();
        }
    }
}
