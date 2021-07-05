<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\AnalyzePhpStanCommand;
use Symfony\Component\Console\Input\InputInterface;

class AnalyzePhpStanCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = AnalyzePhpStanCommand::class;
        $this->baseName = 'analyze:phpstan';
        $this->processCommand = ['/path/to/bin-dir/phpstan', 'analyse', '--ansi', '--foo'];

        parent::setUp();

        $this->input->allows()->getOption('phpstan-help')->andReturnFalse();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--foo'],
        ]);
    }

    public function testWithPhpstanHelpOption(): void
    {
        $this->input = $this->mockery(InputInterface::class);
        $this->input->allows()->getOption('phpstan-help')->andReturnTrue();
        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar'],
        ]);

        $this->processCommand = ['/path/to/bin-dir/phpstan', 'analyse', '--ansi', '--help'];

        $this->testRun();
    }
}
