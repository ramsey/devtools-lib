<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\Lint\AllCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\TestCase;
use Ramsey\Test\Dev\Tools\Command\MockCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class AllCommandTest extends TestCase
{
    private AllCommand $sutCommand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sutCommand = new AllCommand(new Configuration());
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['lint'], $this->sutCommand->getAliases());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('Run all linting checks', $this->sutCommand->getDescription());
    }

    public function testGetHelp(): void
    {
        $this->assertNotEmpty($this->sutCommand->getHelp());
    }

    public function testGetName(): void
    {
        $this->assertSame('lint:all', $this->sutCommand->getName());
    }

    public function testExecute(): void
    {
        $application = new Application();

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(['', '<comment>Executing lint:syntax</comment>']);
        $output->expects('writeln')->with(['', '<comment>Executing lint:style</comment>']);

        $syntax = new MockCommand('lint:syntax');
        $style = new MockCommand('lint:style');

        $application->addCommands([$syntax, $style, $this->sutCommand]);

        $this->assertSame(0, $application->find('lint:all')->run(new StringInput(''), $output));
        $this->assertSame(1, $syntax->calledCount);
        $this->assertSame(1, $style->calledCount);
    }

    public function testExecuteReturnsNonZeroExitCode(): void
    {
        $application = new Application();

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(['', '<comment>Executing lint:syntax</comment>']);
        $output->expects('writeln')->with(['', '<comment>Executing lint:style</comment>']);

        $syntax = new MockCommand('lint:syntax');
        $syntax->exitCode = 2;

        $style = new MockCommand('lint:style');
        $style->exitCode = 3;

        $application->addCommands([$syntax, $style, $this->sutCommand]);

        $this->assertSame(5, $application->find('lint:all')->run(new StringInput(''), $output));
        $this->assertSame(1, $syntax->calledCount);
        $this->assertSame(1, $style->calledCount);
    }
}
