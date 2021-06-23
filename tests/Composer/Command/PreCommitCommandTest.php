<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\PreCommitCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PreCommitCommandTest extends CommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = PreCommitCommand::class;
        $this->baseName = 'pre-commit';

        parent::setUp();
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['pre-commit'], $this->command->getAliases());
    }

    public function testRun(): void
    {
        $input = new StringInput('');
        $output = $this->mockery(NullOutput::class)->makePartial();

        $output
            ->expects()
            ->writeln(
                [
                    '<error>The pre-commit command is deprecated. Running it performs no action.</error>',
                    '<error>Use captainhook.json to define pre-commit actions. This command will</error>',
                    '<error>go away in the next major version of ramsey/devtools-lib.</error>',
                ],
                OutputInterface::VERBOSITY_NORMAL,
            );

        $this->assertSame(0, $this->command->run($input, $output));
    }
}
