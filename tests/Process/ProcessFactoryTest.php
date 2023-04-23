<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Process;

use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Process\Process;

use function dirname;

class ProcessFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $factory = new ProcessFactory();
        $process = $factory->factory(['ls'], dirname(__DIR__), ['FOO' => 'bar']);

        $this->assertInstanceOf(Process::class, $process);
        $this->assertSame(dirname(__DIR__), $process->getWorkingDirectory());
        $this->assertSame(['FOO' => 'bar'], $process->getEnv());
    }
}
