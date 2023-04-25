<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Analyze;

use Composer\Composer;
use Ramsey\Dev\Tools\Command\Analyze\PhpStanCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class PhpStanCommandTest extends ProcessCommandTestCase
{
    public function testGetProcessCommandWithDevtoolsMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'devtools' => ['memory-limit' => '2048M'],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new PhpStanCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', '--error-format=json'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            'analyze',
            '--ansi',
            '--memory-limit=2048M',
            '--error-format=json',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    public function testGetProcessCommandWithCommandSpecificMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'ramsey/devtools' => [
                    'memory-limit' => '2048M',
                    'commands' => [
                        'analyze:phpstan' => [
                            'memory-limit' => '1024M',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new PhpStanCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', '--error-format=json'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            'analyze',
            '--ansi',
            '--memory-limit=1024M',
            '--error-format=json',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['analyse:phpstan'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to PHPStan',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Run static analysis with PHPStan';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'phpstan';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHPStan. For example:

              <code>composer require --dev phpstan/phpstan</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'analyze:phpstan';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable, 'analyze', '--ansi'];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
            [
                'argvInput' => ['foo:command', '--', '--error-format=json'],
                'expected' => [...$baseCommand, '--error-format=json'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new PhpStanCommand($configuration);
    }
}
