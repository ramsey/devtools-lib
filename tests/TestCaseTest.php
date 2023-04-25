<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools;

use Mockery\MockInterface;
use Ramsey\Dev\Tools\TestCase;

class TestCaseTest extends TestCase
{
    /**
     * This test is primarily for coverage.
     */
    public function testMockery(): void
    {
        $mock = $this->mockery(TestCase::class);

        $this->assertInstanceOf(MockInterface::class, $mock);
    }

    /**
     * This test is primarily for coverage.
     */
    public function testMockerySpy(): void
    {
        $spy = $this->mockerySpy(TestCase::class);

        $this->assertInstanceOf(MockInterface::class, $spy);
    }
}
