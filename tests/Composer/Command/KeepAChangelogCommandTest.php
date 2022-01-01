<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\KeepAChangelogCommand;

use const DIRECTORY_SEPARATOR;

class KeepAChangelogCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = KeepAChangelogCommand::class;
        $this->baseName = 'changelog';
        $this->processCommand = ['/path/to/bin-dir' . DIRECTORY_SEPARATOR . 'keep-a-changelog', '--ansi', '--foo'];

        parent::setUp();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--foo'],
        ]);
    }
}
