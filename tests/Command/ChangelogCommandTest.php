<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command;

use Ramsey\Dev\Tools\Command\ChangelogCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;

class ChangelogCommandTest extends ProcessCommandTestCase
{
    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['keep-a-changelog', 'log'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to keep-a-changelog',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Manage your project changelog';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'keep-a-changelog';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install phly/keep-a-changelog. For example:

              <code>composer require --dev phly/keep-a-changelog</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'changelog';
    }

    /**
     * @inheritDoc
     */
    protected function getProcessCommandTests(): array
    {
        $executable = (string) $this->sutCommand->getExecutablePath();

        $baseCommand = [$executable, '--ansi'];

        return [
            [
                'argvInput' => ['foo:command'],
                'expected' => $baseCommand,
            ],
            [
                'argvInput' => ['foo:command', 'entry:added', '--', '--release-version=1.2.3'],
                'expected' => [...$baseCommand, 'entry:added', '--release-version=1.2.3'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new ChangelogCommand($configuration);
    }
}
