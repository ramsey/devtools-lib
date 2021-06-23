<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\LintSyntaxCommand;

class LintSyntaxCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LintSyntaxCommand::class;
        $this->baseName = 'lint:syntax';
        $this->processCommand = [
            '/path/to/bin-dir/parallel-lint',
            '--colors',
            'src',
            'tests',
            '--bar',
            '--baz',
        ];

        parent::setUp();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar', '--baz'],
        ]);
    }
}
