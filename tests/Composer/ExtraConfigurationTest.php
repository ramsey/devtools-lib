<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use Ramsey\Dev\Tools\Composer\ExtraConfiguration;
use Ramsey\Dev\Tools\TestCase;

class ExtraConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $extra = new ExtraConfiguration();

        $this->assertSame('dev', $extra->commandPrefix);
        $this->assertSame([], $extra->commands);
        $this->assertNull($extra->memoryLimit);
    }
}
