<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\LintStyleCommand;
use Symfony\Component\Console\Input\InputInterface;

class LintStyleCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LintStyleCommand::class;
        $this->baseName = 'lint:style';
        $this->processCommand = [
            '/path/to/bin-dir/phpcs',
            '--colors',
            '--cache=build/cache/phpcs.cache',
            '--bar',
            '--baz',
        ];

        parent::setUp();

        $this->input->allows()->getOption('phpcs-help')->andReturnFalse();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar', '--baz'],
        ]);
    }

    public function testWithPhpcsHelpOption(): void
    {
        $this->input = $this->mockery(InputInterface::class);
        $this->input->allows()->getOption('phpcs-help')->andReturnTrue();
        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar'],
        ]);

        $this->processCommand = ['/path/to/bin-dir/phpcs', '--colors', '--cache=build/cache/phpcs.cache', '--help'];

        $this->testRun();
    }
}
