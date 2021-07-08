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

class AnalyzePhpStanCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'analyze:phpstan';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        if ($input->getOption('phpstan-help')) {
            // Ignore all other arguments and display PHPStan help.
            $args = ['--help'];
        }

        return array_merge(
            [
                $this->withBinPath('phpstan'),
                'analyse',
                '--ansi',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs the PHPStan static analyzer.')
            ->addUsage('--phpstan-help')
            ->addUsage('-- [<phpstan-options>...]')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
                new InputOption('phpstan-help', null, InputOption::VALUE_NONE, 'Display PHPStan help'),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes PHPStan, using any
            local configuration files (e.g., phpstan.neon) available.

            If you don't have a configuration file yet, you can get started with:

              <info>%command.full_name% -- src tests</info>

            For more information on PHPStan, see https://phpstan.org

            You may also pass additional arguments to PHPStan. To do so, use a
            double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to PHPStan.

            For example:

              <info>%command.full_name% -- --error-format=json</info>

            To view PHPStan help, use the <info>--phpstan-help</info> option.

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to PHPStan. These include standard
            options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these options,
            invoke PHPStan directly via <info>./vendor/bin/phpstan</info>.
            EOD;
    }
}
