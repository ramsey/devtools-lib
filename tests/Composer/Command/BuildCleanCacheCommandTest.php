<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\BuildCleanCacheCommand;

class BuildCleanCacheCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = BuildCleanCacheCommand::class;
        $this->baseName = 'build:clean:cache';
        $this->processCommand = ['git', 'clean', '-fX', 'build/cache/.'];

        parent::setUp();
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['bar:build:clear-cache'], $this->command->getAliases());
    }

    public function testRun(): void
    {
        $this->output->expects()->writeln('<info>Clearing the build cache...</info>');

        parent::testRun();
    }

    public function testRunWithFailure(): void
    {
        $this->output->expects()->writeln('<info>Clearing the build cache...</info>');
        $this->output->expects()->writeln('<error>Unable to clear the build cache</error>');

        $this->doTestRun(
            function (callable $callback): int {
                $callback('', 'test buffer string');

                return 1;
            },
            1,
        );
    }
}
