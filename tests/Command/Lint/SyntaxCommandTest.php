<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\Lint\SyntaxCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;

class SyntaxCommandTest extends ProcessCommandTestCase
{
    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['parallel-lint'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to parallel-lint',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Check for syntax errors';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'parallel-lint';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHP Parallel Lint. For example:

              <code>composer require --dev php-parallel-lint/php-parallel-lint</code>

            If you'd like colored output with PHP Parallel Lint, also install
            PHP Console Highlighter:

              <code>composer require --dev \
                php-parallel-lint/php-console-highlighter</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'lint:syntax';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable, '--colors', 'src', 'tests'];

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

        return new SyntaxCommand($configuration);
    }
}
