<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Composer\Console\Application;
use Mockery\MockInterface;
use Ramsey\Dev\Tools\Composer\Command\LintCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class LintCommandTest extends CommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LintCommand::class;
        $this->baseName = 'lint:all';

        parent::setUp();
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['lint'], $this->command->getAliases());
    }

    public function testIsProxyCommand(): void
    {
        $this->assertTrue($this->command->isProxyCommand());
    }

    public function testRun(): void
    {
        /** @var Command & MockInterface $commandLintSyntax */
        $commandLintSyntax = $this->mockery(
            Command::class,
            [
                'getName' => 'syntax',
                'run' => 0,
            ],
        );

        /** @var Command & MockInterface $commandLintStyle */
        $commandLintStyle = $this->mockery(
            Command::class,
            [
                'getName' => 'style',
                'run' => 0,
            ],
        );

        $input = new StringInput('');
        $output = new NullOutput();

        /** @var Application & MockInterface $application */
        $application = $this->mockery(
            Application::class,
            [
                'getHelperSet' => $this->mockery(HelperSet::class),
            ],
        );
        $application->shouldReceive('getDefinition')->passthru();
        $application
            ->expects()
            ->find($this->command->withPrefix('lint:syntax'))
            ->andReturn($commandLintSyntax);
        $application
            ->expects()
            ->find($this->command->withPrefix('lint:style'))
            ->andReturn($commandLintStyle);

        $this->command->setApplication($application);

        $this->assertSame(0, $this->command->run($input, $output));
    }
}
