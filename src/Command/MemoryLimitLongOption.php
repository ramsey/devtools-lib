<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Ramsey\Dev\Tools\Composer\ExtraConfiguration;

/**
 * @phpstan-import-type CommandDefinition from ExtraConfiguration
 */
trait MemoryLimitLongOption
{
    /**
     * @return string[]
     */
    private function getMemoryLimitOption(): array
    {
        /** @var CommandDefinition $commandConfig */
        $commandConfig = $this->extra->commands[(string) $this->getName()] ?? [];
        $memoryLimit = $commandConfig['memory-limit'] ?? $this->extra->memoryLimit ?? null;

        if ($memoryLimit !== null) {
            return ["--memory-limit=$memoryLimit"];
        }

        return [];
    }
}
