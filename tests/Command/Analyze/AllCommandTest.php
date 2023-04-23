<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Analyze;

use Ramsey\Dev\Tools\Command\Analyze\AllCommand;
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
        $this->assertSame(['analyse:all', 'analyze', 'analyse'], $this->sutCommand->getAliases());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('Run all static analysis checks', $this->sutCommand->getDescription());
    }

    public function testGetHelp(): void
    {
        $this->assertNotEmpty($this->sutCommand->getHelp());
    }

    public function testGetName(): void
    {
        $this->assertSame('analyze:all', $this->sutCommand->getName());
    }

    public function testExecute(): void
    {
        $application = new Application();

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(['', '<comment>Executing analyze:phpstan</comment>']);
        $output->expects('writeln')->with(['', '<comment>Executing analyze:psalm</comment>']);

        $phpstan = new MockCommand('analyze:phpstan');
        $psalm = new MockCommand('analyze:psalm');

        $application->addCommands([$phpstan, $psalm, $this->sutCommand]);

        $this->assertSame(0, $application->find('analyze:all')->run(new StringInput(''), $output));
        $this->assertSame(1, $phpstan->calledCount);
        $this->assertSame(1, $psalm->calledCount);
    }

    public function testExecuteReturnsNonZeroExitCode(): void
    {
        $application = new Application();

        $output = $this->mockery(OutputInterface::class, [
            'isDebug' => false,
            'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
        ]);
        $output->expects('writeln')->with(['', '<comment>Executing analyze:phpstan</comment>']);
        $output->expects('writeln')->with(['', '<comment>Executing analyze:psalm</comment>']);

        $phpstan = new MockCommand('analyze:phpstan');
        $phpstan->exitCode = 2;

        $psalm = new MockCommand('analyze:psalm');
        $psalm->exitCode = 3;

        $application->addCommands([$phpstan, $psalm, $this->sutCommand]);

        $this->assertSame(5, $application->find('analyze:all')->run(new StringInput(''), $output));
        $this->assertSame(1, $phpstan->calledCount);
        $this->assertSame(1, $psalm->calledCount);
    }
}
