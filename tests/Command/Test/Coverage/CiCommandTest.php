<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Test\Coverage;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Command\Test\Coverage\CiCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;

class CiCommandTest extends ProcessCommandTestCase
{
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
            'tests',
        ];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
            [
                'argvInput' => ['foo:command', '--', '--no-progress', '--no-results'],
                'expected' => [...$baseCommand, '--no-progress', '--no-results'],
            ],
        ];
    }
}
