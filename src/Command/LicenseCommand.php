<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'license',
    description: 'Check the licenses of your Composer dependencies',
    aliases: ['license-checker'],
)]
final class LicenseCommand extends ProcessCommand
{
    public function getExecutableName(): string
    {
        return 'license-checker';
    }

    public function getInstallationMessage(): string
    {
        return <<<'EOD'
            You may use Composer to install madewithlove/license-checker. For example:

              <code>composer require --dev madewithlove/license-checker</code>
            EOD;
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return [(string) $this->getExecutablePath(), '--ansi', ...$args];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument(
                    'args',
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Additional arguments to pass to license-checker',
                ),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <code>%command.name%</code> command executes
            <code>license-checker</code> from the
            <href=https://github.com/madewithlove/license-checker-php>madewithlove/license-checker</>
            package.

            To get started with license-checker, generate a config file based on
            the licenses your project currently uses:

              <code>%command.full_name% generate-config</code>

            Now, check the licenses of Composer dependencies to see if they match
            what your project allows:

              <code>%command.full_name% check</code>

            You may also pass arguments and options to license-checker. To do so,
            use a double-dash (<code>--</code>) to indicate all following
            arguments and options should be passed along directly to
            license-checker.

            Examples:

              <code>%command.full_name% count</code>
              <code>%command.full_name% check -- --no-dev</code>

            You may pass additional options to license-checker by using a
            double-dash (<code>--</code>) to indicate all following arguments
            and options should pass directly to license-checker.

            To view all license-checker sub-commands, use the <code>list</code>
            command:

              <code>%command.full_name% list</code>

            For more information on License Checker, see
            <link>https://github.com/madewithlove/license-checker-php</link>.
            EOD;
    }
}
