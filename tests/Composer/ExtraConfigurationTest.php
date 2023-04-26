<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use PHPUnit\Framework\Attributes\TestWith;
use Ramsey\Dev\Tools\Composer\ExtraConfiguration;
use Ramsey\Dev\Tools\TestCase;

class ExtraConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $extra = new ExtraConfiguration('foobar', 'cmd');

        $this->assertSame('foobar', $extra->commandName);
        $this->assertSame('cmd', $extra->commandPrefix);
        $this->assertSame([], $extra->scripts);
        $this->assertFalse($extra->override);
        $this->assertNull($extra->memoryLimit);
        $this->assertSame('cmd:foobar', $extra->getPrefixedCommandName());
    }

    #[TestWith(['', 'foobar'])]
    #[TestWith([':', 'foobar'])]
    #[TestWith(['abc:', 'abc:foobar'])]
    #[TestWith(['def', 'def:foobar'])]
    #[TestWith(['ghi::::::', 'ghi:foobar'])]
    public function testCommandPrefixes(string $prefix, string $expectedName): void
    {
        $extra = new ExtraConfiguration(commandName: 'foobar', commandPrefix: $prefix);

        $this->assertSame($expectedName, $extra->getPrefixedCommandName());
    }
}
