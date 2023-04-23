<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Lint;

use Ramsey\Dev\Tools\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function sprintf;

#[AsCommand(
    name: 'lint:all',
    description: 'Run all linting checks',
    aliases: ['lint'],
)]
final class AllCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp($this->getHelpText());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        assert($application instanceof Application);

        $syntax = $application->find('lint:syntax');
        $style = $application->find('lint:style');

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $syntax->getName())]);
        $syntaxExit = $syntax->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $style->getName())]);
        $styleExit = $style->run($input, $output);

        return $syntaxExit + $styleExit;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            <code>%command.name%</code> is a shortcut to execute
            <code>lint:syntax</code> and <code>lint:style</code>.
            EOD;
    }
}
