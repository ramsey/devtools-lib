<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command;

use Ramsey\Dev\Tools\Command\LicenseCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;

class LicenseCommandTest extends ProcessCommandTestCase
{
    /**
     * @inheritDoc
     */
    protected function getExpectedAliases(): array
    {
        return ['license-checker'];
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedDefinition(): array
    {
        return [
            'args' => [
                'name' => 'args',
                'description' => 'Additional arguments to pass to license-checker',
            ],
        ];
    }

    protected function getExpectedDescription(): string
    {
        return 'Check the licenses of your Composer dependencies';
    }

    protected function getExpectedExecutableName(): string
    {
        return 'license-checker';
    }

    protected function getExpectedInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install madewithlove/license-checker. For example:

              <code>composer require --dev madewithlove/license-checker</code>
            EOD;
    }

    protected function getExpectedName(): string
    {
        return 'license';
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
                'argvInput' => ['foo:command', 'generate-config', '--', '--filename=licenses.txt'],
                'expected' => [...$baseCommand, 'generate-config', '--filename=licenses.txt'],
            ],
        ];
    }

    protected function getSutCommand(): ProcessCommand
    {
        $configuration = new Configuration();

        return new LicenseCommand($configuration);
    }
}
