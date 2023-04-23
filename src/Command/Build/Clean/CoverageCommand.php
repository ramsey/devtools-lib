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
    name: 'build:clean:coverage',
    description: 'Clean the project build/coverage/ directory',
)]
final class CoverageCommand extends ProcessCommand
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
        return [(string) $this->getExecutablePath(), 'clean', '-fX', 'build/coverage/.'];
    }

    protected function configure(): void
    {
        $this->setHelp($this->getHelpText());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Clearing the coverage reports...</info>');

        $exitCode = parent::doExecute($input, $output);

        if ($exitCode !== Command::SUCCESS) {
            $output->writeln('<error>Unable to clear the coverage reports</error>');
        }

        return $exitCode;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command will erase everything from
            the <file>build/coverage/</file> directory that isn't committed to
            Git.

            This is helpful to clean up cached HTML or XML files from coverage
            reports.

            This command erases only the contents of <file>build/coverage/</file>,
            while <code>build:clean</code> erases everything else from the
            <file>build/</file> directory. If you wish to keep other build
            artifacts and erase only the coverage, <code>%command.name%</code>
            is the command to use.
            EOD;
    }
}
