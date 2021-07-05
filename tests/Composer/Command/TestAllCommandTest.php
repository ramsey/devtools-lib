<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Composer\Console\Application;
use Ramsey\Dev\Tools\Composer\Command\TestAllCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class TestAllCommandTest extends CommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = TestAllCommand::class;
        $this->baseName = 'test:all';

        parent::setUp();
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['test'], $this->command->getAliases());
    }

    public function testIsProxyCommand(): void
    {
        $this->assertTrue($this->command->isProxyCommand());
    }

    public function testRun(): void
    {
        $commandLint = $this->mockery(Command::class, [
            'getName' => 'lint',
            'run' => 0,
        ]);

        $commandAnalyze = $this->mockery(Command::class, [
            'getName' => 'analyze',
            'run' => 0,
        ]);

        $commandTest = $this->mockery(Command::class, [
            'getName' => 'test',
            'run' => 0,
        ]);

        $input = new StringInput('');
        $output = new NullOutput();

        $application = $this->mockery(Application::class, [
            'getHelperSet' => $this->mockery(HelperSet::class),
        ]);
        $application->shouldReceive('getDefinition')->passthru();
        $application
            ->expects()
            ->find($this->command->withPrefix('lint:all'))
            ->andReturn($commandLint);
        $application
            ->expects()
            ->find($this->command->withPrefix('analyze:all'))
            ->andReturn($commandAnalyze);
        $application
            ->expects()
            ->find($this->command->withPrefix('test:unit'))
            ->andReturn($commandTest);

        $this->command->setApplication($application);

        $this->assertSame(0, $this->command->run($input, $output));
    }
}
