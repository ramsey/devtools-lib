<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Analyze;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'analyze:psalm',
    description: 'Run static analysis with Psalm',
    aliases: ['analyse:psalm'],
)]
final class PsalmCommand extends ProcessCommand
{
    public function getExecutableName(): string
    {
        return 'psalm';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install Psalm. For example:

              <code>composer require --dev vimeo/psalm</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return [(string) $this->getExecutablePath(), ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to Psalm',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes Psalm, using any
            local configuration files (e.g., <file>psalm.xml</file>) available.

            Examples:

              <code>%command.full_name%</code>
              <code>%command.full_name% -- --init</code>
              <code>%command.full_name% -- --alter</code>
              <code>%command.full_name% -- --help</code>

            You may pass additional options to Psalm by using a double-dash
            (<code>--</code>) to indicate all following arguments and options
            should pass directly to Psalm.

            For more information on Psalm, see <link>https://psalm.dev</link>.
            EOD;
    }
}
