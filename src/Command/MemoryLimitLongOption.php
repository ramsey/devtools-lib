<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

trait MemoryLimitLongOption
{
    /**
     * @return string[]
     */
    private function getMemoryLimitOption(): array
    {
        if ($this->getExtra()->memoryLimit !== null) {
            return ["--memory-limit={$this->getExtra()->memoryLimit}"];
        }

        return [];
    }
}
