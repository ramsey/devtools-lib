<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Lint;

use Composer\Composer;
use Ramsey\Dev\Tools\Command\Lint\StyleCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class StyleCommandTest extends ProcessCommandTestCase
{
    public function testGetProcessCommandWithDevtoolsMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'devtools' => ['memory-limit' => '384M'],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new StyleCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', 'src/File1.php'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--colors',
            '--cache=build/cache/phpcs.cache',
            '-d',
            'memory_limit=384M',
            'src/File1.php',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    public function testGetProcessCommandWithCommandSpecificMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'ramsey/devtools' => [
                    'memory-limit' => '384M',
                    'commands' => [
                        'lint:style' => [
                            'memory-limit' => '768M',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new StyleCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', 'src/File2.php'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--colors',
            '--cache=build/cache/phpcs.cache',
            '-d',
            'memory_limit=768M',
            'src/File2.php',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['phpcs'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to phpcs',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Check for coding standards issues';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'phpcs';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            phpcs is part of the PHP_CodeSniffer package. You may use
            Composer to install PHP_CodeSniffer. For example:

              <code>composer require --dev squizlabs/php_codesniffer</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'lint:style';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable, '--colors', '--cache=build/cache/phpcs.cache'];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
            [
                'argvInput' => ['foo:command', '--', '--help'],
                'expected' => [...$baseCommand, '--help'],
            ],
            [
                'argvInput' => ['foo:command', '--', 'src/File1.php', 'src/File2.php'],
                'expected' => [...$baseCommand, 'src/File1.php', 'src/File2.php'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new StyleCommand($configuration);
    }
}
