<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

/**
 * Represents configuration for ramsey/devtools found in `composer.json` within
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
 *
 * @phpstan-type CommandName = string
 * @phpstan-type CommandDefinition = array{override?: bool, script: string | string[], memory-limit?: int | string}
 * @phpstan-type Commands = array<CommandName, CommandDefinition>
 * @phpstan-type DevToolsConfig = array{command-prefix?: string, commands?: Commands, memory-limit?: int | string}
 */
final class ExtraConfiguration
{
    public const DEFAULT_COMMAND_PREFIX = 'dev';

    /**
     * @link https://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes PHP's shorthand bytes options
     *
     * @param string $commandPrefix The prefix to use with devtools commands. To
     *     disable the prefix, set to an empty string.
     * @param Commands $commands An array of key-value pairs where the key is a
     *     command name to extend or override and the value is the definition to
     *     use when extending or overriding the command.
     * @param int | string | null $memoryLimit When set, this memory limit will
     *     apply to all commands that support setting a memory limit. If this is
     *     an integer, it represents bytes; it may use the same shorthand byte
     *     values that PHP supports (i.e., K, M, and G).
     */
    public function __construct(
        public readonly string $commandPrefix = self::DEFAULT_COMMAND_PREFIX,
        public readonly array $commands = [],
        public readonly int | string | null $memoryLimit = null,
    ) {
    }
}
