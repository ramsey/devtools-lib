<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\Lint\StyleCommand;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Test\Dev\Tools\Command\ProcessCommandTestCase;

class StyleCommandTest extends ProcessCommandTestCase
{
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
