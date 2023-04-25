<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

use function rtrim;

/**
 * Represents configuration for a command, as found in `composer.json` within
 * the `extra` property. For example:
 *
 * ```json
 * {
 *     "extra": {
 *         "ramsey/devtools": {
 *             "command-prefix": "dev",
 *             "commands": {
 *                 "lint:structure": {
 *                     "override": true,
 *                     "script": [
 *                         "./tools/my-structure-check"
 *                     ],
 *                     "memory-limit": "1G"
 *                 }
 *             },
 *             "memory-limit": "512M"
 *         }
 *     }
 * }
 * ```
 *
 * Instead of "ramsey/devtools," you may use "devtools" as the property name.
 *
 * To configure a custom property name for your own plugin:
 *
 * ```php
 * $appConfig = new \Ramsey\Dev\Tools\Configuration(composerExtraProperty: 'my-devtools');
 * $app = new \Ramsey\Dev\Tools\DevToolsApplication($appConfig);
 * ```
 */
final class ExtraConfiguration
{
    public const DEFAULT_COMMAND_PREFIX = 'dev';

    /**
     * @link https://getcomposer.org/doc/articles/scripts.md#writing-custom-commands Composer's custom commands
     * @link https://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes PHP's shorthand bytes options
     *
     * @param string $commandName The name of the command to which this
     *     configuration applies.
     * @param string $commandPrefix The prefix to apply to this command. To
     *     disable the prefix, set to an empty string.
     * @param string[] $scripts Additional scripts to run when the named command
     *     is executed. These scripts work just like custom commands in Composer.
     * @param bool $override Whether to override the built-in command. When true,
     *     only the scripts defined for the command in composer.json will run.
     * @param int | string | null $memoryLimit When set, this memory limit will
     *     apply to all commands that support setting a memory limit. If this is
     *     an integer, it represents bytes; it may use the same shorthand byte
     *     values that PHP supports (i.e., K, M, and G).
     */
    public function __construct(
        public readonly string $commandName,
        public readonly string $commandPrefix = self::DEFAULT_COMMAND_PREFIX,
        public readonly array $scripts = [],
        public readonly bool $override = false,
        public readonly int | string | null $memoryLimit = null,
    ) {
    }

    public function getPrefixedCommandName(): string
    {
        if ($this->commandPrefix !== '' && $this->commandPrefix !== ':') {
            return rtrim($this->commandPrefix, ':') . ':' . $this->commandName;
        }

        return $this->commandName;
    }
}
