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

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use const PHP_EOL;

/**
 * @deprecated The pre-commit command is deprecated. Running it performs no
 *     action. Use captainhook.json to define pre-commit actions. This command
 *     will go away in the next major version of ramsey/devtools-lib.
 */
class PreCommitCommand extends BaseCommand
{
    public function getBaseName(): string
    {
        return 'pre-commit';
    }

    /**
     * Supports the use of `composer pre-commit`, without the command prefix/namespace
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return ['pre-commit'];
    }

    protected function configure(): void
    {
        $this
            ->setDescription('This command is deprecated.')
            ->setHelp(
                'The pre-commit command is deprecated. Running it performs no action.' . PHP_EOL
                . 'Use captainhook.json to define pre-commit actions. This command will' . PHP_EOL
                . 'go away in the next major version of ramsey/devtools-lib.',
            )
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }

    /**
     * @throws Exception
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            [
                '<error>The pre-commit command is deprecated. Running it performs no action.</error>',
                '<error>Use captainhook.json to define pre-commit actions. This command will</error>',
                '<error>go away in the next major version of ramsey/devtools-lib.</error>',
            ],
            OutputInterface::VERBOSITY_NORMAL,
        );

        return 0;
    }
}
