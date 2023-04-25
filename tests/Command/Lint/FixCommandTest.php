<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Lint;

use Composer\Composer;
use Ramsey\Dev\Tools\Command\Lint\FixCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Process\Process;

class FixCommandTest extends ProcessCommandTestCase
{
    public function testGetProcessCommandWithDevtoolsMemoryLimit(): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => [
                'devtools' => ['memory-limit' => '128M'],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new FixCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', 'src/File1.php'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--cache=build/cache/phpcs.cache',
            '-d',
            'memory_limit=128M',
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
                    'memory-limit' => '128M',
                    'commands' => [
                        'lint:fix' => [
                            'memory-limit' => '512M',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $configuration = new Configuration(composerFactory: $composerFactory);
        $command = new FixCommand($configuration);

        $input = new ArgvInput(['foo:command', '--', 'src/File2.php'], $command->getDefinition());
        $output = new NullOutput();

        $expectedProcessCommand = [
            (string) $command->getExecutablePath(),
            '--cache=build/cache/phpcs.cache',
            '-d',
            'memory_limit=512M',
            'src/File2.php',
        ];

        $this->assertSame($expectedProcessCommand, $command->getProcessCommand($input, $output));
    }

    public function testExecuteWithSuccess(): void
    {
        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');

        // A value of one (1) for phpcbf is not a failure.
        $process->expects('wait')->andReturns(1);

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/phpcbf');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new FixCommand($configuration);

        $input = new StringInput('');
        $output = new NullOutput();

        $this->assertSame(Command::SUCCESS, $command->run($input, $output));
    }

    public function testExecuteWithFailure(): void
    {
        $process = $this->mockery(Process::class);
        $process->allows('setTimeout');
        $process->allows('setTty');
        $process->expects('start');

        // Anything from 2 and greater is a failure with phpcbf.
        $process->expects('wait')->andReturns(2);

        $processFactory = $this->mockery(ProcessFactory::class);
        $processFactory->expects('findExecutable')->andReturns('/path/to/phpcbf');
        $processFactory->expects('factory')->andReturns($process);

        $configuration = new Configuration(processFactory: $processFactory);
        $command = new FixCommand($configuration);

        $input = new StringInput('');
        $output = new NullOutput();

        $this->assertSame(2, $command->run($input, $output));
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['phpcbf'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to phpcbf',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Auto-fix coding standards issues, if possible';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'phpcbf';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            phpcbf is part of the PHP_CodeSniffer package. You may use
            Composer to install PHP_CodeSniffer. For example:

              <code>composer require --dev squizlabs/php_codesniffer</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'lint:fix';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable, '--cache=build/cache/phpcs.cache'];

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

        return new FixCommand($configuration);
    }
}
