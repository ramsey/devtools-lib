<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Test;

use Ramsey\Dev\Tools\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function sprintf;

#[AsCommand(
    name: 'test:all',
    description: 'Run linting checks, static analysis, and unit tests',
    aliases: ['test'],
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

        $lint = $application->find('lint:all');
        $analyze = $application->find('analyze:all');
        $test = $application->find('test:unit');

        $lintExit = $lint->run($input, $output);
        $analyzeExit = $analyze->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $test->getName())]);
        $testExit = $test->run($input, $output);

        return $lintExit + $analyzeExit + $testExit;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            <code>%command.name%</code> is a shortcut to execute
            <code>lint:all</code>, <code>lint:analyze</code>, and
            <code>test:unit</code>.
            EOD;
    }
}
