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

class TestCoverageCiCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'test:coverage:ci';
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
                $this->withBinPath('phpunit'),
                '--colors=always',
                '--coverage-clover',
                'build/coverage/clover.xml',
                '--coverage-xml',
                'build/coverage/coverage-xml',
                '--log-junit',
                'build/coverage/junit.xml',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs the unit test suite and generates coverage reports for CI workflows.')
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }
}
