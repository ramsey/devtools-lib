<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Process;

use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $factory = new ProcessFactory();

        $this->assertInstanceOf(Process::class, $factory->factory(['ls']));
    }
}
