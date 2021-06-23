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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class LintCommand extends BaseCommand
{
    public function getBaseName(): string
    {
        return 'lint:all';
    }

    /**
     * Supports the use of `composer lint`, without the command prefix/namespace
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return ['lint'];
    }

    protected function configure(): void
    {
        $this->setDescription('Runs all linting checks against the source code.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $lintPds = $this->getApplication()->find($this->withPrefix('lint:pds'));
        $lintSyntax = $this->getApplication()->find($this->withPrefix('lint:syntax'));
        $lintStyle = $this->getApplication()->find($this->withPrefix('lint:style'));

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $lintPds->getName())]);
        $lintPdsExit = $lintPds->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $lintSyntax->getName())]);
        $lintSyntaxExit = $lintSyntax->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $lintStyle->getName())]);
        $lintStyleExit = $lintStyle->run($input, $output);

        return $lintPdsExit + $lintSyntaxExit + $lintStyleExit;
    }
}
