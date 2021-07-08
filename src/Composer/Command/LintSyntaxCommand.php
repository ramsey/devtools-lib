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
use function count;

class LintSyntaxCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'lint:syntax';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        if (count($args) === 0) {
            $args = ['src', 'tests'];
        }

        if ($input->getOption('parallel-lint-help')) {
            // Ignore all other arguments and display phpcs help.
            $args = ['--help'];
        }

        return array_merge(
            [
                $this->withBinPath('parallel-lint'),
                '--colors',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Checks for syntax errors.')
            ->addUsage('--parallel-lint-help')
            ->addUsage('-- [<parallel-lint-options>...]')
            ->addUsage('-- [<file>...]')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
                new InputOption('parallel-lint-help', null, InputOption::VALUE_NONE, 'Display parallel-lint help'),
            ]);
    }

    private function getHelpText(): string
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>parallel-lint</info> from
            the php-parallel-lint/php-parallel-lint package.

            parallel-lint is similar to <info>php -l</info>, but for large programs, it can
            run faster, since it runs the checks in parallel.

            You may also pass arguments and options to parallel-lint. To do so,
            use a double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to parallel-lint.

            For example:

              <info>%command.full_name% -- src/File1.php src/File2.php</info>

            To view parallel-lint help, use the <info>--parallel-lint-help</info> option.

            For more information on parallel-lint, see
            https://github.com/php-parallel-lint/PHP-Parallel-Lint

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to parallel-lint. These include
            standard options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these
            options, invoke parallel-lint directly via <info>./vendor/bin/parallel-lint</info>.
            EOD;
    }
}
