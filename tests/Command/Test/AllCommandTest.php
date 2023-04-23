<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Test;

use Ramsey\Dev\Tools\Command\Test\AllCommand;
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
        $this->assertSame(['test'], $this->sutCommand->getAliases());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('Run linting checks, static analysis, and unit tests', $this->sutCommand->getDescription());
    }

    public function testGetHelp(): void
    {
        $this->assertNotEmpty($this->sutCommand->getHelp());
    }

    public function testGetName(): void
    {
        $this->assertSame('test:all', $this->sutCommand->getName());
    }

    public function testExecute(): void
    {
        $application = new Application();

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(['', '<comment>Executing test:unit</comment>']);

        $lint = new MockCommand('lint:all');
        $analyze = new MockCommand('analyze:all');
        $unit = new MockCommand('test:unit');

        $application->addCommands([$lint, $analyze, $unit, $this->sutCommand]);

        $this->assertSame(0, $application->find('test:all')->run(new StringInput(''), $output));
        $this->assertSame(1, $lint->calledCount);
        $this->assertSame(1, $analyze->calledCount);
        $this->assertSame(1, $unit->calledCount);
    }

    public function testExecuteReturnsNonZeroExitCode(): void
    {
        $application = new Application();

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(['', '<comment>Executing test:unit</comment>']);

        $lint = new MockCommand('lint:all');
        $lint->exitCode = 2;

        $analyze = new MockCommand('analyze:all');
        $analyze->exitCode = 3;

        $unit = new MockCommand('test:unit');
        $unit->exitCode = 5;

        $application->addCommands([$lint, $analyze, $unit, $this->sutCommand]);

        $this->assertSame(10, $application->find('test:all')->run(new StringInput(''), $output));
        $this->assertSame(1, $lint->calledCount);
        $this->assertSame(1, $analyze->calledCount);
        $this->assertSame(1, $unit->calledCount);
    }
}
