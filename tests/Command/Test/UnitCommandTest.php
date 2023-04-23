<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Test;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Command\Test\UnitCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;

class UnitCommandTest extends ProcessCommandTestCase
{
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
