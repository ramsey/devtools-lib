<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\TestCase;

class FactoryTest extends TestCase
{
    public function testGetComposer(): void
    {
        $factory = new Factory();

        $this->assertSame('ramsey/devtools-lib', $factory->getComposer()->getPackage()->getName());
    }
}
