<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Test;

use Ramsey\Dev\Tools\Command\MemoryLimitIniOption;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test:unit',
    description: 'Run unit tests with PHPUnit',
)]
final class UnitCommand extends ProcessCommand
{
    use MemoryLimitIniOption;

    public function getExecutableName(): string
    {
        return 'phpunit';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHPUnit. For example:

              <code>composer require --dev phpunit/phpunit</code>
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

        return [(string) $this->getExecutablePath(), '--colors=always', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to PHPUnit',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>phpunit</code>.
            It uses any local configuration files (e.g., <file>phpunit.xml</file>)
            available.

            Examples:

              <code>%command.full_name%</code>
              <code>%command.full_name% tests/File1Test.php</code>
              <code>%command.full_name% -- --group functional</code>
              <code>%command.full_name% -- --help</code>

            You may pass additional options to phpunit by using a double-dash
            (<code>--</code>) to indicate all following arguments and options
            should pass directly to phpunit.

            For more information on PHPUnit, see <link>https://phpunit.de</link>.
            EOD;
    }
}
