<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Build;

use Ramsey\Dev\Tools\Command\Build\CleanCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CleanCommandTest extends ProcessCommandTestCase
{
    public function testExecuteWithSuccess(): void
    {
        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');
        $process->expects('wait')->andReturns(Command::SUCCESS);

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/git');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new CleanCommand($configuration);

        $input = new StringInput('');

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with('<info>Cleaning the build directory...</info>');

        $this->assertSame(Command::SUCCESS, $command->run($input, $output));
    }

    public function testExecuteWithFailure(): void
    {
        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');
        $process->expects('wait')->andReturns(Command::FAILURE);

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/git');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new CleanCommand($configuration);

        $input = new StringInput('');

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with('<info>Cleaning the build directory...</info>');
        $output->expects('writeln')->with('<error>Unable to clean the build directory</error>');

        $this->assertSame(Command::FAILURE, $command->run($input, $output));
    }

    protected function getExpectedDescription(): string
    {
        return 'Clean the project build/ directory';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'git';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            See the Git documentation for details on how to install
            Git on your system:

              <link>https://git-scm.com/book/en/v2/Getting-Started-Installing-Git</link>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'build:clean';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => [...$baseCommand, 'clean', '-fX', 'build/.'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new CleanCommand($configuration);
    }
}
