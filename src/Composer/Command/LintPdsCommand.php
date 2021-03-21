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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function stripos;

class LintPdsCommand extends ProcessCommand
{
    private const FAILURE_TOKEN = 'incorrect';

    private bool $validationFailed = false;

    public function getBaseName(): string
    {
        return 'lint:pds';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        return [$this->withBinPath('pds-skeleton'), 'validate'];
    }

    protected function configure(): void
    {
        $this->setDescription('Validates project compliance with pds/skeleton.');
    }

    protected function getProcessCallback(OutputInterface $output): callable
    {
        return function (string $type, string $buffer) use ($output): void {
            if (stripos($buffer, self::FAILURE_TOKEN) !== false) {
                $this->validationFailed = true;
            }

            $output->write($buffer);
        };
    }

    /**
     * Since pds-skeleton always returns a 0 exit code, we check to see if the
     * output contains the word "Incorrect." If so, we return a 1 to indicate
     * failure.
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::doExecute($input, $output);

        if ($this->validationFailed === true) {
            $this->validationFailed = false;

            return 1;
        }

        return $exitCode;
    }
}
