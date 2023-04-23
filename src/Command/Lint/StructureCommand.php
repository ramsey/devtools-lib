<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Lint;

use Closure;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function stripos;

#[AsCommand(
    name: 'lint:structure',
    description: 'Validate project compliance with pds/skeleton',
    aliases: ['pds-skeleton'],
)]
final class StructureCommand extends ProcessCommand
{
    private const FAILURE_TOKEN = 'incorrect';

    private bool $validationFailed = false;

    public function getExecutableName(): string
    {
        return 'pds-skeleton';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PDS Skeleton. For example:

              <code>composer require --dev pds/skeleton</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return [(string) $this->getExecutablePath(), 'validate', ...$args];
    }

    protected function getProcessCallback(OutputInterface $output): Closure
    {
        return function (string $type, string $buffer) use ($output): void {
            if (stripos($buffer, self::FAILURE_TOKEN) !== false) {
                $this->validationFailed = true;
            }

            $output->write($buffer);
        };
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::doExecute($input, $output);

        if ($this->validationFailed === true) {
            $this->validationFailed = false;

            return Command::FAILURE;
        }

        return $exitCode;
    }

    protected function configure(): void
    {
        $this->setHelp($this->getHelpText());
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>pds-skeleton
            validate</code> from the <href=https://github.com/php-pds/skeleton>pds/skeleton</>
            package.

            pds/skeleton describes a standard project structure suitable for
            all PHP packages. This includes files and folders that are common
            practice in PHP development, such as <file>bin/</file> for command
            line executables, <file>src/</file> for PHP source code, and
            <file>README(.*)</file> for information about the package.

            This validator catches common alternate names for these files and
            directories and suggests renaming them to the community standard
            names.

            For more information on pds/skeleton, see
            <link>https://github.com/php-pds/skeleton</link>.
            EOD;
    }
}
