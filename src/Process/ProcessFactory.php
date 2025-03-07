<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Process;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Factory to create a Process instance for running commands
 */
class ProcessFactory
{
    /**
     * @param string[] $command
     * @param array<string, string> | null $env
     */
    public function factory(array $command, ?string $cwd = null, ?array $env = null): Process
    {
        return new Process($command, $cwd, $env);
    }

    public function findExecutable(string $name): ?string
    {
        return (new ExecutableFinder())->find($name);
    }
}
