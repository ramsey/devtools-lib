<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Mockery\MockInterface;
use Ramsey\Dev\Tools\Composer\Command\AnalyzeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class AnalyzeCommandTest extends CommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = AnalyzeCommand::class;
        $this->baseName = 'analyze:all';

        parent::setUp();
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['analyze'], $this->command->getAliases());
    }

    public function testIsProxyCommand(): void
    {
        $this->assertTrue($this->command->isProxyCommand());
    }

    public function testRun(): void
    {
        /** @var Command & MockInterface $commandPhpStan */
        $commandPhpStan = $this->mockery(Command::class, [
            'getName' => 'phpstan',
            'run' => 0,
        ]);

        /** @var Command & MockInterface $commandPsalm */
        $commandPsalm = $this->mockery(Command::class, [
            'getName' => 'psalm',
            'run' => 0,
        ]);

        $input = new StringInput('');
        $output = new NullOutput();

        $application = $this->mockApplication();
        $application
            ->expects()
            ->find($this->command->withPrefix('analyze:phpstan'))
            ->andReturn($commandPhpStan);
        $application
            ->expects()
            ->find($this->command->withPrefix('analyze:psalm'))
            ->andReturn($commandPsalm);

        $this->command->setApplication($application);

        $this->assertSame(0, $this->command->run($input, $output));
    }
}
