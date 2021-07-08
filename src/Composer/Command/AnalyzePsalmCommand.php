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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class AnalyzePsalmCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'analyze:psalm';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        if ($input->getOption('psalm-help')) {
            // Ignore all other arguments and display PHPStan help.
            $args = ['--help'];
        }

        return array_merge(
            [
                $this->withBinPath('psalm'),
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs the Psalm static analyzer.')
            ->addUsage('--psalm-help')
            ->addUsage('-- [<psalm-options>...]')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
                new InputOption('psalm-help', null, InputOption::VALUE_NONE, 'Display Psalm help'),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes Psalm, using any
            local configuration files (e.g., psalm.xml) available.

            To get started with Psalm, first generate a config file:

              <info>%command.full_name% -- --init</info>

            Then, run Psalm:

              <info>%command.full_name%</info>

            For more information on Psalm, see https://psalm.dev

            You may also pass additional arguments to Psalm. To do so, use a
            double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to Psalm.

            For example:

              <info>%command.full_name% -- --update-baseline</info>

            To view Psalm help, use the <info>--psalm-help</info> option.

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to Psalm. These include standard
            options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these options,
            invoke Psalm directly via <info>./vendor/bin/psalm</info>.
            EOD;
    }
}
