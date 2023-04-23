<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Build\Clean;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'build:clean:cache',
    description: 'Clean the project build/cache/ directory',
)]
final class CacheCommand extends ProcessCommand
{
    public function getExecutableName(): string
    {
        return 'git';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            See the Git documentation for details on how to install
            Git on your system:

              <link>https://git-scm.com/book/en/v2/Getting-Started-Installing-Git</link>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        return [(string) $this->getExecutablePath(), 'clean', '-fX', 'build/cache/.'];
    }

    protected function configure(): void
    {
        $this->setHelp($this->getHelpText());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Clearing the build cache...</info>');

        $exitCode = parent::doExecute($input, $output);

        if ($exitCode !== Command::SUCCESS) {
            $output->writeln('<error>Unable to clear the build cache</error>');
        }

        return $exitCode;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command will erase everything from
            the <file>build/cache/</file> directory that isn't committed to Git.

            Many tools, such as PHPUnit, Psalm, PHPStan, Doctrine, and more,
            cache files to speed up their processes. You may use the
            <file>build/cache/</file> directory as a place to store the cache
            for any such tools. Nothing in <file>build/cache/</file> is under
            version control.

            This command erases only the contents of <file>build/cache/</file>,
            while <code>build:clean</code> erases everything from the
            <file>build/</file> directory. If you wish to keep other build
            artifacts and erase only the cache, <code>%command.name%</code> is
            the command to use.
            EOD;
    }
}
