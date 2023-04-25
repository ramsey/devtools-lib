<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Test;

use Composer\Composer;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Command\Test\UnitCommand;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class UnitCommandTest extends ProcessCommandTestCase
{
    public function testGetProcessCommandWithDevtoolsMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'devtools' => ['memory-limit' => '6G'],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new UnitCommand($configuration);

        $input = new ArgvInput(['foo:command'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--colors=always',
            '-d',
            'memory_limit=6G',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    public function testGetProcessCommandWithCommandSpecificMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'ramsey/devtools' => [
                    'memory-limit' => '6G',
                    'commands' => [
                        'test:unit' => [
                            'memory-limit' => '7G',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new UnitCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', '--no-progress'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--colors=always',
            '-d',
            'memory_limit=7G',
            '--no-progress',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new UnitCommand($configuration);
    }

    protected function getExpectedDescription(): string
    {
        return 'Run unit tests with PHPUnit';
    }

    protected function getExpectedName(): string
    {
        return 'test:unit';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'phpunit';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHPUnit. For example:

              <code>composer require --dev phpunit/phpunit</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to PHPUnit',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => [$executable, '--colors=always'],
            ],
            [
                'argvInput' => ['foo:command', '--', '--no-progress', '--no-results'],
                'expected' => [$executable, '--colors=always', '--no-progress', '--no-results'],
            ],
        ];
    }
}
