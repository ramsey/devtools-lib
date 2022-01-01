<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\LicenseCheckerCommand;

use const DIRECTORY_SEPARATOR;

class LicenseCheckerCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LicenseCheckerCommand::class;
        $this->baseName = 'license';
        $this->processCommand = ['/path/to/bin-dir' . DIRECTORY_SEPARATOR . 'license-checker', '--ansi', '--foo'];

        parent::setUp();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--foo'],
        ]);
    }
}
