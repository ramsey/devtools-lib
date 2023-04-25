<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Analyze;

use Ramsey\Dev\Tools\Command\MemoryLimitLongOption;
use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'analyze:phpstan',
    description: 'Run static analysis with PHPStan',
    aliases: ['analyse:phpstan'],
)]
final class PhpStanCommand extends ProcessCommand
{
    use MemoryLimitLongOption;

    public function getExecutableName(): string
    {
        return 'phpstan';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHPStan. For example:

              <code>composer require --dev phpstan/phpstan</code>
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

        return [(string) $this->getExecutablePath(), 'analyze', '--ansi', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to PHPStan',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes PHPStan, using any
            local configuration files (e.g., <file>phpstan.neon</file>)
            available.

            Examples:

              <code>%command.full_name%</code>
              <code>%command.full_name% -- --level=3</code>
              <code>%command.full_name% -- --help</code>

            You may pass additional options to PHPStan by using a double-dash
            (<code>--</code>) to indicate all following arguments and options
            should pass directly to PHPStan.

            For more information on PHPStan, see <link>https://phpstan.org</link>.
            EOD;
    }
}
