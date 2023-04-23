<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command;

use Closure;
use Mockery\MockInterface;
use Ramsey\Dev\Tools\Command\ExecutableNotFound;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProcessCommandTest extends TestCase
{
    private Configuration $configuration;
    private ProcessFactory & MockInterface $processFactory;
    private ProcessCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->processFactory = $this->mockery(ProcessFactory::class);

        $this->configuration = new Configuration(processFactory: $this->processFactory);

        $this->command = new class ($this->configuration) extends ProcessCommand {
            public function getExecutableName(): string
            {
                return 'foo';
            }

            public function getInstallationMessage(): string
            {
                return <<<'EOD'
                    You may use Composer to install it. For example:

                        composer require --dev foo/foo
                    EOD;
            }

            /**
             * @inheritDoc
             */
            public function getProcessCommand(InputInterface $input, OutputInterface $output): array
            {
                return [(string) $this->getExecutablePath(), '--bar'];
            }
        };
    }

    public function testGetExecutablePathReturnsNullWhenUnableToFindExecutable(): void
    {
        $this->processFactory->expects('findExecutable')->with('foo')->andReturnNull();

        $this->assertNull($this->command->getExecutablePath());
    }

    public function testHasExecutableReturnsFalseWhenUnableToFindExecutable(): void
    {
        $this->processFactory->expects('findExecutable')->with('foo')->andReturnNull();

        $this->assertFalse($this->command->hasExecutable());
    }

    public function testExecuteThrowsExceptionWhenExecutableNotFound(): void
    {
        $this->processFactory->expects('findExecutable')->with('foo')->andReturnNull();

        $expectedExceptionMessage = <<<'EOD'
            Unable to find foo in your PATH. Perhaps it's not installed?

            You may use Composer to install it. For example:

                composer require --dev foo/foo
            EOD;

        $this->expectException(ExecutableNotFound::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->command->run(new StringInput(''), new NullOutput());
    }

    public function testExecution(): void
    {
        $process = $this->mockery(Process::class);
        $process->expects('setTimeout')->with(300);
        $process->allows('setTty');
        $process->expects('start');
        $process->expects('wait')->withArgs(function (Closure $callback): bool {
            $callback(Process::ERR, 'this is error output');
            $callback(Process::OUT, 'this is standard output');

            return true;
        })->andReturns(Command::SUCCESS);

        $output = $this->mockery(ConsoleOutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('getErrorOutput->write')->with('this is error output');
        $output->expects('write')->with('this is standard output');

        $this->processFactory->expects('findExecutable')->with('foo')->andReturns('/path/to/foo');

        $this->processFactory
            ->expects('factory')
            ->with(
                ['/path/to/foo', '--bar'],
                $this->configuration->projectRoot,
                [
                    'PATH' => $this->configuration->execPath,
                    'RAMSEY_DEVTOOLS' => 'true',
                ],
            )
            ->andReturns($process);

        $this->command->run(new StringInput(''), $output);
    }

    public function testSetHelpOverrideShowLocationOfExecutable(): void
    {
        $config = new Configuration();

        $processCommand = new class ($config) extends ProcessCommand {
            public function getExecutableName(): string
            {
                return 'php';
            }

            public function getInstallationMessage(): string
            {
                return 'This is how you install it.';
            }

            /** @inheritDoc */
            public function getProcessCommand(InputInterface $input, OutputInterface $output): array
            {
                return [];
            }
        };

        $processCommand->setHelp('foo');

        $format = <<<'EOD'
            foo

            ---

            This command uses php. On your system, php is located at:%w<file>%s/php</file>
            EOD;

        $this->assertStringMatchesFormat($format, $processCommand->getHelp());
    }

    public function testSetHelpOverrideExplainsHowToInstallExecutable(): void
    {
        $config = new Configuration();

        $processCommand = new class ($config) extends ProcessCommand {
            public function getExecutableName(): string
            {
                return 'could_not_find_this';
            }

            public function getInstallationMessage(): string
            {
                return 'This is how you install it.';
            }

            /** @inheritDoc */
            public function getProcessCommand(InputInterface $input, OutputInterface $output): array
            {
                return [];
            }
        };

        $processCommand->setHelp('foo');

        $format = <<<'EOD'
            foo

            ---

            This command uses could_not_find_this. On your system, could_not_find_this is
            <error>not installed</error>.

            This is how you install it.
            EOD;

        $this->assertSame($format, $processCommand->getHelp());
    }
}
