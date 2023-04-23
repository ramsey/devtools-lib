<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command\Test\Coverage;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test:coverage:ci',
    description: 'Run tests and generate coverage reports for CI',
)]
final class CiCommand extends ProcessCommand
{
    public function getExecutableName(): string
    {
        return 'phpunit';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install PHPUnit. For example:

              <code>composer require --dev phpunit/phpunit</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return [
            (string) $this->getExecutablePath(),
            '--colors=always',
            '--coverage-text',
            '--coverage-clover',
            'build/coverage/clover.xml',
            '--coverage-cobertura',
            'build/coverage/cobertura.xml',
            '--coverage-crap4j',
            'build/coverage/crap4j.xml',
            '--coverage-xml',
            'build/coverage/coverage-xml',
            '--log-junit',
            'build/junit.xml',
            'tests',
            ...$args,
        ];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to PHPUnit',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes <code>phpunit</code>,
            generating coverage reports and test logging suitable for CI
            (continuous integration) workflows. It uses any local configuration
            files (e.g., <file>phpunit.xml</file>) available.

            Coverage reports include:

              * Clover XML, saved to <file>build/coverage/clover.xml</file>
              * Cobertura XML, saved to <file>build/coverage/cobertura.xml</file>
              * Crap4J XML, saved to <file>build/coverage/crap4j.xml</file>
              * PHPUnit XML, saved to <file>build/coverage/coverage-xml/</file>

            Test logs include:

              * JUnit XML, saved to <file>build/junit.xml</file>

            For more information on phpunit, see <link>https://phpunit.de</link>.

            You may extend or override this command for your own needs. See the
            ramsey/devtools <file>README.md</file> file for more information.
            EOD;
    }
}
