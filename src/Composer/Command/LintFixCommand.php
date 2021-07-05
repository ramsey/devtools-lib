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

class LintFixCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'lint:fix';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        if ($input->getOption('phpcbf-help')) {
            // Ignore all other arguments and display phpcbf help.
            $args = ['--help'];
        }

        return array_merge(
            [
                $this->withBinPath('phpcbf'),
                '--cache=build/cache/phpcs.cache',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Auto-fixes coding standards issues, if possible.')
            ->addUsage('--phpcbf-help')
            ->addUsage('-- [<phpcbf-options>...]')
            ->addUsage('-- [<file>...]')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
                new InputOption('phpcbf-help', null, InputOption::VALUE_NONE, 'Display phpcbf help'),
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
            The <info>%command.name%</info> command executes <info>phpcbf</info> (PHP Code Beautifier
            and Fixer), part of PHP_CodeSniffer. It uses any local configuration
            files (e.g., phpcs.xml) available.

            You may also pass additional arguments to phpcbf. To do so, use a
            double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to phpcbf.

            For example:

              <info>%command.full_name% -- src/File1.php src/File2.php</info>

            To view phpcbf help, use the <info>--phpcbf-help</info> option.

            For more information on phpcbf, see
            https://github.com/squizlabs/PHP_CodeSniffer

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to phpcbf. These include standard
            options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these options,
            invoke phpcbf directly via <info>./vendor/bin/phpcbf</info>.
            EOD;
    }
}
