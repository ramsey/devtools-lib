<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

trait MemoryLimitIniOption
{
    /**
     * @return string[]
     */
    private function getMemoryLimitOption(): array
    {
        if ($this->getExtra()->memoryLimit !== null) {
            return ['-d', "memory_limit={$this->getExtra()->memoryLimit}"];
        }

        return [];
    }
}
