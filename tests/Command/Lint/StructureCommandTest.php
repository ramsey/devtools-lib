<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\Lint\StructureCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class StructureCommandTest extends ProcessCommandTestCase
{
    public function testExecuteWithSuccess(): void
    {
        $input = new StringInput('');
        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('write')->with('Everything is correct');

        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');
        $process->expects('wait')->andReturnUsing(function (callable $callback): int {
            $callback('foo', 'Everything is correct');

            return Command::SUCCESS;
        });

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/pds-skeleton');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new StructureCommand($configuration);

        $this->assertSame(Command::SUCCESS, $command->run($input, $output));
    }

    public function testExecuteWithFailure(): void
    {
        $input = new StringInput('');
        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('write')->with('Everything is incorrect');

        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');
        $process->expects('wait')->andReturnUsing(function (callable $callback): int {
            $callback('foo', 'Everything is incorrect');

            return Command::SUCCESS;
        });

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/pds-skeleton');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new StructureCommand($configuration);

        $this->assertSame(Command::FAILURE, $command->run($input, $output));
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['pds-skeleton'];
    }

    protected function getExpectedDescription(): string
    {
        return 'Validate project compliance with pds/skeleton';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'pds-skeleton';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PDS Skeleton. For example:

              <code>composer require --dev pds/skeleton</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'lint:structure';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable, 'validate'];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new StructureCommand($configuration);
    }
}
