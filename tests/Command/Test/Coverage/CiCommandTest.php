<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Test\Coverage;

use Composer\Composer;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Command\Test\Coverage\CiCommand;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class CiCommandTest extends ProcessCommandTestCase
{
    public function testGetProcessCommandWithDevtoolsMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'devtools' => ['memory-limit' => '4G'],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new CiCommand($configuration);

        $input = new ArgvInput(['foo:command'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--colors=always',
            '--coverage-text',
            '--coverage-clover',
            'build/coverage/clover.xml',
            '--coverage-cobertura',
            'build/coverage/cobertura.xml',
            '--coverage-crap4j',
            'build/coverage/crap4j.xml',
            '--coverage-xml',
            'build/coverage/coverage-xml',
            '--log-junit',
            'build/junit.xml',
            '-d',
            'memory_limit=4G',
            'tests',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    public function testGetProcessCommandWithCommandSpecificMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'ramsey/devtools' => [
                    'memory-limit' => '4G',
                    'commands' => [
                        'test:coverage:ci' => [
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
        $command = new CiCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', '--no-progress'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--colors=always',
            '--coverage-text',
            '--coverage-clover',
            'build/coverage/clover.xml',
            '--coverage-cobertura',
            'build/coverage/cobertura.xml',
            '--coverage-crap4j',
            'build/coverage/crap4j.xml',
            '--coverage-xml',
            'build/coverage/coverage-xml',
            '--log-junit',
            'build/junit.xml',
            '-d',
            'memory_limit=2G',
            '--no-progress',
            'tests',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new CiCommand($configuration);
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

    protected function getExpectedDescription(): string
    {
        return 'Run tests and generate coverage reports for CI';
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

    protected function getExpectedName(): string
    {
        return 'test:coverage:ci';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [
            $executable,
            '--colors=always',
            '--coverage-text',
            '--coverage-clover',
            'build/coverage/clover.xml',
            '--coverage-cobertura',
            'build/coverage/cobertura.xml',
            '--coverage-crap4j',
            'build/coverage/crap4j.xml',
            '--coverage-xml',
            'build/coverage/coverage-xml',
            '--log-junit',
            'build/junit.xml',
        ];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => [...$baseCommand, 'tests'],
            ],
            [
                'argvInput' => ['foo:command', '--', '--no-progress', '--no-results'],
                'expected' => [...$baseCommand, '--no-progress', '--no-results', 'tests'],
            ],
        ];
    }
}
