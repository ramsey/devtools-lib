<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Analyze;

use Composer\Composer;
use Ramsey\Dev\Tools\Command\Analyze\PsalmCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class PsalmCommandTest extends ProcessCommandTestCase
{
    public function testGetProcessCommandWithDevtoolsMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'devtools' => ['memory-limit' => '1G'],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new PsalmCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', '--no-progress'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--memory-limit=1G',
            '--no-progress',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    public function testGetProcessCommandWithCommandSpecificMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'ramsey/devtools' => [
                    'memory-limit' => '1G',
                    'commands' => [
                        'analyze:psalm' => [
                            'memory-limit' => '2G',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new PsalmCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', '--stats'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--memory-limit=2G',
            '--stats',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['analyse:psalm'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to Psalm',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Run static analysis with Psalm';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'psalm';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install Psalm. For example:

              <code>composer require --dev vimeo/psalm</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'analyze:psalm';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
            [
                'argvInput' => ['foo:command', '--', '--no-progress', '--stats'],
                'expected' => [...$baseCommand, '--no-progress', '--stats'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new PsalmCommand($configuration);
    }
}
