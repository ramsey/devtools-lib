<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Analyze;

use Ramsey\Dev\Tools\Command\Analyze\PhpStanCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;

class PhpStanCommandTest extends ProcessCommandTestCase
{
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
