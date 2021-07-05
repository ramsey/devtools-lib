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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class LicenseCheckerCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'license';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return array_merge(
            [
                $this->withBinPath('license-checker'),
                '--ansi',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription('Checks dependency licenses.')
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }

    private function getHelpText(): string
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>license-checker</info> from
            the <href=https://packagist.org/packages/madewithlove/license-checker>madewithlove/license-checker</> package.

            You may also pass arguments and options to license-checker. To do so,
            use a double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to license-checker.

            For example:

              <info>%command.full_name% -- used</info>
              <info>%command.full_name% -- help generate-config</info>

            To view all license-checker sub-commands, use the <info>list</info> command:

              <info>%command.full_name% -- list</info>

            For more information on License Checker, see
            https://github.com/madewithlove/license-checker-php

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to license-checker. These include
            standard options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these
            options, invoke license-checker directly via
            <info>./vendor/bin/license-checker</info>.
            EOD;
    }
}
