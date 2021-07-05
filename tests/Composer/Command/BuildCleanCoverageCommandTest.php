<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\BuildCleanCoverageCommand;

class BuildCleanCoverageCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = BuildCleanCoverageCommand::class;
        $this->baseName = 'build:clean:coverage';
        $this->processCommand = ['git', 'clean', '-fX', 'build/coverage/.'];

        parent::setUp();
    }

    public function testRun(): void
    {
        $this->output->expects()->writeln('<info>Clearing the coverage reports...</info>');

        parent::testRun();
    }

    public function testRunWithFailure(): void
    {
        $this->output->expects()->writeln('<info>Clearing the coverage reports...</info>');
        $this->output->expects()->writeln('<error>Unable to clear the coverage reports</error>');

        $this->doTestRun(
            function (callable $callback): int {
                $callback('', 'test buffer string');

                return 1;
            },
            1,
        );
    }
}
