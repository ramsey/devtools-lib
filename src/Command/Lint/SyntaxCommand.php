<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'lint:syntax',
    description: 'Check for syntax errors',
    aliases: ['parallel-lint'],
)]
final class SyntaxCommand extends ProcessCommand
{
    public function getExecutableName(): string
    {
        return 'parallel-lint';
    }

    public function getInstallationMessage(): string
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

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return [(string) $this->getExecutablePath(), '--colors', 'src', 'tests', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to parallel-lint',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>parallel-lint</code>
            from the <href=https://github.com/php-parallel-lint/PHP-Parallel-Lint>php-parallel-lint/php-parallel-lint</>
            package.

            parallel-lint is similar to <code>php -l</code>, but it runs
            recursively to check all PHP files in a directory, provides friendly
            output, and runs lint-checking jobs in parallel.

            Examples:

              <code>%command.full_name%</code>
              <code>%command.full_name% src/File1.php</code>
              <code>%command.full_name% -- --help</code>

            You may pass additional options to parallel-lint by using a
            double-dash (<code>--</code>) to indicate all following arguments
            and options should pass directly to parallel-lint.

            For more information on parallel-lint, see
            <link>https://github.com/php-parallel-lint/PHP-Parallel-Lint</link>
            EOD;
    }
}
