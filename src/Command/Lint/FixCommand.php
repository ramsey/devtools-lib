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
    name: 'lint:fix',
    description: 'Auto-fix coding standards issues, if possible',
    aliases: ['phpcbf'],
)]
final class FixCommand extends ProcessCommand
{
    use MemoryLimitIniOption;

    public function getExecutableName(): string
    {
        return 'phpcbf';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            phpcbf is part of the PHP_CodeSniffer package. You may use
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

        return [(string) $this->getExecutablePath(), '--cache=build/cache/phpcs.cache', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to phpcbf',
                ),
            ]);
    }

    /**
     * This returns a 0 if phpcbf returns either a 0 or a 1. phpcbf returns the
     * following exit codes:
     *
     * * Exit code 0 is used to indicate that no fixable errors were found, so
     *   nothing was fixed
     * * Exit code 1 is used to indicate that all fixable errors were fixed correctly
     * * Exit code 2 is used to indicate that phpcbf failed to fix some of the
     *   fixable errors it found
     * * Exit code 3 is used for general script execution errors
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/1818#issuecomment-354420927
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::doExecute($input, $output);

        if ($exitCode > 1) {
            return $exitCode;
        }

        return 0;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>phpcbf</code>,
            the PHP Code Beautifier and Fixer, part of the PHP_CodeSniffer
            package. It uses any local configuration files (e.g.,
            <file>phpcs.xml</file>) available.

            Examples:

              <code>%command.full_name%</code>
              <code>%command.full_name% src/File1.php</code>
              <code>%command.full_name% -- --help</code>

            You may pass additional options to phpcbf by using a double-dash
            (<code>--</code>) to indicate all following arguments and options
            should pass directly to phpcbf.

            For more information on phpcbf, see
            <link>https://github.com/squizlabs/PHP_CodeSniffer</link>.
            EOD;
    }
}
