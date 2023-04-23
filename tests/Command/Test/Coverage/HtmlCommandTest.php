<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Test\Coverage;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Command\Test\Coverage\HtmlCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class HtmlCommandTest extends ProcessCommandTestCase
{
    public function testExecuteWithSuccess(): void
    {
        $input = new StringInput('');
        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(
            ['', '<info>HTML coverage report is available in build/coverage/coverage-html/index.html.</info>'],
        );

        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');
        $process->expects('wait')->andReturns(Command::SUCCESS);

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/pds-skeleton');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new HtmlCommand($configuration);

        $this->assertSame(Command::SUCCESS, $command->run($input, $output));
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['test:coverage'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to PHPUnit',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Run tests and generate HTML coverage reports';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'phpunit';
    }

    protected function getExpectedName(): string
    {
        return 'test:coverage:html';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHPUnit. For example:

              <code>composer require --dev phpunit/phpunit</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [
            $executable,
            '--colors=always',
            '--coverage-html',
            'build/coverage/coverage-html',
            'tests',
        ];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
            [
                'argvInput' => ['foo:command', '--', '--no-progress', '--no-results'],
                'expected' => [...$baseCommand, '--no-progress', '--no-results'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new HtmlCommand($configuration);
    }
}
