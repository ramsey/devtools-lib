<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\LintStyleCommand;

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

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar', '--baz'],
        ]);
    }
}
