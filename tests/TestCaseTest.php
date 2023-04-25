<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools;

use Mockery\MockInterface;
use Ramsey\Dev\Tools\TestCase;
use ReflectionProperty;

class TestCaseTest extends TestCase
{
    public function testMockery(): void
    {
        $mock = $this->mockery(TestCase::class);

        $property = new ReflectionProperty($mock, '_mockery_ignoreMissing');
        $shouldIgnoreMissing = $property->getValue($mock);

        $this->assertInstanceOf(MockInterface::class, $mock);
        $this->assertFalse($shouldIgnoreMissing);
    }

    public function testMockerySpy(): void
    {
        $spy = $this->mockerySpy(TestCase::class);

        $property = new ReflectionProperty($spy, '_mockery_ignoreMissing');
        $shouldIgnoreMissing = $property->getValue($spy);

        $this->assertInstanceOf(MockInterface::class, $spy);
        $this->assertTrue($shouldIgnoreMissing);
    }
}
