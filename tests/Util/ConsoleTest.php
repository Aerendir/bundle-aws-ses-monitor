<?php

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Util\Console;
use SerendipityHQ\Bundle\ConsoleStyles\Console\Formatter\SerendipityHQOutputFormatter;
use SerendipityHQ\Bundle\ConsoleStyles\Console\Style\SerendipityHQStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * {@inheritdoc}
 */
final class ConsoleTest extends TestCase
{
    public function testCreateWriter(): void
    {
        $resource            = new Console();
        $mockInputInterface  = $this->createMock(InputInterface::class);
        $mockOutputInterface = $this->createMock(OutputInterface::class);
        $mockFormatter       = $this->createMock(SerendipityHQOutputFormatter::class);
        $mockOutputInterface->method('getFormatter')->willReturn($mockFormatter);

        $result = $resource->createWriter($mockInputInterface, $mockOutputInterface);

        self::assertInstanceOf(SerendipityHQStyle::class, $result);
    }

    public function testCreateSection4(): void
    {
        if (Kernel::MAJOR_VERSION < 4) {
            self::markTestSkipped('To run only on SF4');

            return;
        }

        $resource            = new Console();
        $mockOutputInterface = $this->createMock(ConsoleOutput::class);

        // The mocked ConsoleOutput on SF4 have the "::section()" method
        $result = $resource->createSection($mockOutputInterface);

        self::assertInstanceOf(ConsoleSectionOutput::class, $result);
    }

    public function testCreateSection3(): void
    {
        $resource            = new Console();
        $mockOutputInterface = $this->createMock(ConsoleSectionOutput::class);

        // The mocked OutputInterface doesn't have the "::section()" method
        $result = $resource->createSection($mockOutputInterface);

        self::assertSame($mockOutputInterface, $result);
    }

    public function testOverwrite4(): void
    {
        if (Kernel::MAJOR_VERSION < 4) {
            self::markTestSkipped('To run only on SF4');

            return;
        }

        $resource            = new Console();
        $mockOutputInterface = $this->createMock(ConsoleSectionOutput::class);
        $testString          = 'Serendipity HQ is very awesome!';

        $mockOutputInterface->expects(self::once())->method('overwrite')->with($testString);
        $mockOutputInterface->expects(self::never())->method('writeln');

        $resource->overwrite($testString, $mockOutputInterface);
    }

    public function testOverwrite3(): void
    {
        $resource            = new Console();
        $mockOutputInterface = $this->createMock(OutputInterface::class);
        $testString          = 'Serendipity HQ is very awesome!';

        $mockOutputInterface->expects(self::once())->method('writeln')->with($testString);

        $resource->overwrite($testString, $mockOutputInterface);
    }

    public function testClear4(): void
    {
        if (Kernel::MAJOR_VERSION < 4) {
            self::markTestSkipped('To run only on SF4');

            return;
        }

        $resource            = new Console();
        $mockOutputInterface = $this->createMock(ConsoleSectionOutput::class);

        $mockOutputInterface->expects(self::once())->method('clear');

        $resource->clear($mockOutputInterface);
    }

    public function testClear3(): void
    {
        $resource            = new Console();
        $mockOutputInterface = $this->createMock(OutputInterface::class);

        $mockOutputInterface->expects(self::never())->method(self::anything());

        $resource->clear($mockOutputInterface);
    }

    public function testEnableFullLog4(): void
    {
        if (Kernel::MAJOR_VERSION < 4) {
            self::markTestSkipped('To run only on SF4');

            return;
        }

        $resource            = new Console();
        $mockOutputInterface = $this->createMock(ConsoleSectionOutput::class);
        $testString          = 'Serendipity HQ is very awesome!';

        $mockOutputInterface->expects(self::once())->method('writeln')->with($testString);
        $mockOutputInterface->expects(self::never())->method('overwrite');
        $mockOutputInterface->expects(self::never())->method('clear');

        $resource->enableFullLog(true);

        $resource->overwrite($testString, $mockOutputInterface);
        $resource->clear($mockOutputInterface);
    }
}
