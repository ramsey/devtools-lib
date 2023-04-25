<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\MemoryLimitIniOption;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'lint:style',
    description: 'Check for coding standards issues',
    aliases: ['phpcs'],
)]
final class StyleCommand extends ProcessCommand
{
    use MemoryLimitIniOption;

    public function getExecutableName(): string
    {
        return 'phpcs';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            phpcs is part of the PHP_CodeSniffer package. You may use
            Composer to install PHP_CodeSniffer. For example:

              <code>composer require --dev squizlabs/php_codesniffer</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];
        $args = [...$this->getMemoryLimitOption(), ...$args];

        return [(string) $this->getExecutablePath(), '--colors', '--cache=build/cache/phpcs.cache', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to phpcs',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>phpcs</code>,
            PHP_CodeSniffer. It uses any local configuration files (e.g.,
            <file>phpcs.xml</file>) available.

            Examples:

              <code>%command.full_name%</code>
              <code>%command.full_name% src/File1.php</code>
              <code>%command.full_name% -- --help</code>

            You may pass additional options to phpcs by using a double-dash
            (<code>--</code>) to indicate all following arguments and options
            should pass directly to phpcs.

            For more information on phpcs, see
            <link>https://github.com/squizlabs/PHP_CodeSniffer</link>.
            EOD;
    }
}
