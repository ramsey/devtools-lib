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
    name: 'test:coverage:html',
    description: 'Run tests and generate HTML coverage reports',
    aliases: ['test:coverage'],
)]
final class HtmlCommand extends ProcessCommand
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
            '--coverage-html',
            'build/coverage/coverage-html',
            'tests',
            ...$args,
        ];
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::doExecute($input, $output);

        $output->writeln(
            ['', '<info>HTML coverage report is available in build/coverage/coverage-html/index.html.</info>'],
        );

        return $exitCode;
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
            generating a coverage report in HTML format. It uses any local
            configuration files (e.g., <file>phpunit.xml</file>) available.

            The HTML coverage report is saved to
            <file>build/coverage/coverage-html/</file>.

            For more information on PHPUnit, see <link>https://phpunit.de</link>.

            You may extend or override this command for your own needs. See the
            ramsey/devtools <file>README.md</file> file for more information.
            EOD;
    }
}
