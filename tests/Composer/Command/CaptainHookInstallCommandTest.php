<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\CaptainHookInstallCommand;

use const DIRECTORY_SEPARATOR;

class CaptainHookInstallCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = CaptainHookInstallCommand::class;
        $this->baseName = 'captainhook:install';
        $this->processCommand = [
            '/path/to/bin-dir' . DIRECTORY_SEPARATOR . 'captainhook',
            'install',
            '--ansi',
            '-f',
            '-s',
        ];

        parent::setUp();
    }

    public function testRun(): void
    {
        $this->output->expects()->writeln('<info>captainhook/captainhook:</info> Installing hooks...');
        $this->output->expects()->writeln('<info>captainhook/captainhook:</info> ...done installing hooks');

        parent::testRun();
    }

    public function testRunWithFailure(): void
    {
        $this->output->expects()->writeln('<error>captainhook/captainhook: Unable to install hooks</error>');
        $this->output->expects()->writeln('<info>captainhook/captainhook:</info> Installing hooks...');

        $this->doTestRun(
            function (callable $callback): int {
                $callback('', 'test buffer string');

                return -1;
            },
            -1,
        );
    }
}
