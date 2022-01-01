<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Process;

use Mockery\MockInterface;
use Ramsey\Dev\Tools\Process\Process;
use Ramsey\Dev\Tools\TestCase;

use function strtoupper;
use function substr;

use const PHP_OS_FAMILY;

class ProcessTest extends TestCase
{
    public function testUseCorrectCommand(): void
    {
        /** @var Process & MockInterface $process */
        $process = $this->mockery(Process::class);
        $process->shouldAllowMockingProtectedMethods();
        $process->shouldReceive('useCorrectCommand')->passthru();
        $process->expects()->getProcessClassName()->andReturn(ProcessMock::class);

        /** @phpstan-ignore-next-line */
        $commandLine = $process->useCorrectCommand(['foo', '--bar', '--baz']);

        if (strtoupper(substr(PHP_OS_FAMILY, 0, 3)) === 'WIN') {
            $this->assertSame('foo "--bar" "--baz"', $commandLine);
        } else {
            $this->assertSame("foo '--bar' '--baz'", $commandLine);
        }
    }
}
