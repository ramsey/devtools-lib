<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Build;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'build:clean',
    description: 'Clean the project build/ directory',
)]
final class CleanCommand extends ProcessCommand
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
        return [(string) $this->getExecutablePath(), 'clean', '-fX', 'build/.'];
    }

    protected function configure(): void
    {
        $this->setHelp($this->getHelpText());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Cleaning the build directory...</info>');

        $exitCode = parent::doExecute($input, $output);

        if ($exitCode !== Command::SUCCESS) {
            $output->writeln('<error>Unable to clean the build directory</error>');
        }

        return $exitCode;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command will erase everything from
            the <file>build/</file> directory that isn't committed to Git.

            You may use the <file>build/</file> directory to store any artifacts
            your program produces that you do not wish to have under version
            control.

            By default, the <file>build/</file> directory includes subdirectories
            for <file>cache/</file> and <file>coverage/</file> reports, as well
            as a <file>.gitignore</file> file and several <file>.gitkeep</file>
            files. Anything else you place here will be ignored by Git, unless
            you modify the <file>.gitignore</file> file.
            EOD;
    }
}
