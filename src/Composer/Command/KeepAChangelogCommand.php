<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * ramsey/devtools-lib is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class KeepAChangelogCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'changelog';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return array_merge(
            [
                $this->withBinPath('keep-a-changelog'),
                '--ansi',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Support for working with Keep A Changelog.')
            ->setHelp($this->getHelpText())
            ->addUsage('-- list')
            ->addUsage('-- <command-name> [<args>...]')
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>keep-a-changelog</info> from
            the phly/keep-a-changelog package.

            You may also pass arguments and options to keep-a-changelog. To do so,
            use a double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to keep-a-changelog.

            For example:

              <info>%command.full_name% -- version:list</info>
              <info>%command.full_name% -- help entry:added</info>

            To view all keep-a-changelog sub-commands, use the <info>list</info> command:

              <info>%command.full_name% -- list</info>

            For more information on Keep A Changelog, see
            https://keepachangelog.com and
            https://phly.github.io/keep-a-changelog/

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to keep-a-changelog. These include
            standard options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these
            options, invoke keep-a-changelog directly via
            <info>./vendor/bin/keep-a-changelog</info>.
            EOD;
    }
}
