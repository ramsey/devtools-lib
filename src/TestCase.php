<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * A base test case for common test functionality
 */
abstract class TestCase extends PHPUnitTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Configures and returns a mock object with proper type-hinting
     * for static analysis tools
     *
     * @param class-string<T> $class
     * @param mixed ...$arguments
     *
     * @return T & MockInterface
     *
     * @template T
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function mockery(string $class, ...$arguments)
    {
        /** @var T & MockInterface */
        return Mockery::mock($class, ...$arguments);
    }

    /**
     * Configures and returns a mock object for spying with proper type-hinting
     * for static analysis tools
     *
     * @param class-string<T> $class
     * @param mixed ...$arguments
     *
     * @return T & MockInterface
     *
     * @template T
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function mockerySpy(string $class, ...$arguments)
    {
        /** @var T & MockInterface */
        return Mockery::spy($class, ...$arguments);
    }
}
