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

class BuildCleanCacheCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'build:clean:cache';
    }

    /**
     * The use of build:clear-cache is deprecated and will be removed in a
     * future version of ramsey/devtools-lib.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return [
            $this->withPrefix('build:clear-cache'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        return ['git', 'clean', '-fX', 'build/cache/.'];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription(
                'Cleans the build/cache/ directory.',
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Clearing the build cache...</info>');

        $exitCode = parent::doExecute($input, $output);

        if ($exitCode !== 0) {
            $output->writeln('<error>Unable to clear the build cache</error>');
        }

        return $exitCode;
    }

    private function getHelpText(): string
    {
        $buildClean = $this->withPrefix('build:clean');

        return <<<EOD
            The <info>%command.name%</info> command will erase everything from the
            <info>build/cache/</info> directory that isn't committed to Git.

            Many tools, such as PHPUnit, Psalm, PHPStan, Doctrine, and more, cache
            files to speed up their processes. You may use the <info>build/cache/</info>
            directory as a place to store the cache for any such tools. Nothing in
            <info>build/cache/</info> is under version control.

            This command erases only the contents of <info>build/cache/</info>, while
            <info>{$buildClean}</info> erases everything else from the <info>build/</info> directory. If you
            wish to keep other build artifacts and erase only the cache,
            <info>%command.name%</info> is the command to use.
            EOD;
    }
}
