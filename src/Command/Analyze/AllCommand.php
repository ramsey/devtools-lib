<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Analyze;

use Ramsey\Dev\Tools\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function sprintf;

#[AsCommand(
    name: 'analyze:all',
    description: 'Run all static analysis checks',
    aliases: ['analyse:all', 'analyze', 'analyse'],
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

        $phpstan = $application->find('analyze:phpstan');
        $psalm = $application->find('analyze:psalm');

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $phpstan->getName())]);
        $phpstanExit = $phpstan->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $psalm->getName())]);
        $psalmExit = $psalm->run($input, $output);

        return $phpstanExit + $psalmExit;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            <code>%command.name%</code> is a shortcut to execute both the
            <code>analyze:phpstan</code> and <code>analyze:psalm</code>
            commands.
            EOD;
    }
}
