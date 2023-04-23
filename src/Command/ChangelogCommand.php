<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'changelog',
    description: 'Manage your project changelog',
    aliases: ['keep-a-changelog', 'log'],
)]
final class ChangelogCommand extends ProcessCommand
{
    public function getExecutableName(): string
    {
        return 'keep-a-changelog';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install phly/keep-a-changelog. For example:

              <code>composer require --dev phly/keep-a-changelog</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return [(string) $this->getExecutablePath(), '--ansi', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to keep-a-changelog',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>keep-a-changelog</code>
            from the <href=https://github.com/phly/keep-a-changelog>phly/keep-a-changelog</>
            package.

            To get started with keep-a-changelog, use the
            <code>changelog:new</code> command:

              <code>%command.full_name% changelog:new</code>

            Examples:

              <code>%command.full_name% changelog:new</code>
              <code>%command.full_name% version:list</code>
              <code>%command.full_name% entry:added -- --help</code>

            You may pass additional options to keep-a-changelog by using a
            double-dash (<code>--</code>) to indicate all following arguments
            and options should pass directly to keep-a-changelog.

            To view all keep-a-changelog sub-commands, use the
            <code>list</code> command:

              <code>%command.full_name% list</code>

            For more information on Keep A Changelog, see
            <link>https://keepachangelog.com</link> and
            <link>https://phly.github.io/keep-a-changelog/</link>.
            EOD;
    }
}
