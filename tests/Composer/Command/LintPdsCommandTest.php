<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Mockery\MockInterface;
use Ramsey\Dev\Tools\Composer\Command\LintPdsCommand;
use Ramsey\Dev\Tools\Process\Process;

use const DIRECTORY_SEPARATOR;

class LintPdsCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LintPdsCommand::class;
        $this->baseName = 'lint:pds';
        $this->processCommand = ['/path/to/bin-dir' . DIRECTORY_SEPARATOR . 'pds-skeleton', 'validate'];

        parent::setUp();
    }

    public function testRunReturnsExitCode1WhenIncorrectIsPresent(): void
    {
        /** @var Process & MockInterface $process */
        $process = $this->mockery(Process::class);
        $process->expects('setTimeout');
        $process->expects()->start();
        $process
            ->shouldReceive('wait')
            ->once()
            ->andReturnUsing(function (callable $callback): int {
                $callback('', 'test buffer Incorrect string');

                return 0;
            });

        $this->processFactory
            ->expects()
            ->factory($this->processCommand, $this->repositoryRoot)
            ->andReturn($process);

        $this->input->shouldIgnoreMissing();
        $this->output->expects()->write('test buffer Incorrect string');

        $this->assertSame(1, $this->command->run($this->input, $this->output));
    }
}
