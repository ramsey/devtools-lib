<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Analyze;

use Ramsey\Dev\Tools\Command\Analyze\PsalmCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;

class PsalmCommandTest extends ProcessCommandTestCase
{
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
