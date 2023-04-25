<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\PackageEvent;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Ramsey\Dev\Tools\Command\Command;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\DevToolsApplication;
use Symfony\Component\Filesystem\Filesystem;

use function array_values;
use function assert;
use function in_array;

/**
 * Provides a variety of Composer commands and events useful for PHP
 * library and application development
 */
class DevToolsPlugin implements
    Capable,
    CommandProvider,
    PluginInterface
{
    private const BUILD_GITIGNORE_BASE = "\n*\n!.gitignore\n";
    private const BUILD_GITIGNORE_CACHE = "\ncache/*\n!cache\n!cache/.gitkeep\n";
    private const BUILD_GITIGNORE_COVERAGE = "\ncoverage/*\n!coverage\n!coverage/.gitkeep\n";

    public static function setupBuildDirectory(
        ?PackageEvent $event = null,
        Filesystem $filesystem = new Filesystem(),
        ?IOInterface $io = null,
    ): void {
        $io = $event?->getIO() ?? $io;

        if (!$filesystem->exists('./build')) {
            $io?->write('<comment>Creating build directory</comment>');
            $filesystem->mkdir('./build');
            $filesystem->appendToFile('./build/.gitignore', self::BUILD_GITIGNORE_BASE);
        }

        if (!$filesystem->exists('./build/cache')) {
            $io?->write('<comment>Creating build/cache directory</comment>');
            $filesystem->mkdir('./build/cache');
            $filesystem->touch('./build/cache/.gitkeep');
            $filesystem->appendToFile('./build/.gitignore', self::BUILD_GITIGNORE_CACHE);
        }

        if (!$filesystem->exists('./build/coverage')) {
            $io?->write('<comment>Creating build/coverage directory</comment>');
            $filesystem->mkdir('./build/coverage');
            $filesystem->touch('./build/coverage/.gitkeep');
            $filesystem->appendToFile('./build/.gitignore', self::BUILD_GITIGNORE_COVERAGE);
        }
    }

    private readonly DevToolsApplication $application;

    /**
     * @param mixed[] $ctorArgs An array of constructor args Composer passes
     *     when instantiating plugins. This argument is not part of the Composer
     *     plugin interface, however.
     *
     * @phpstan-ignore-next-line
     */
    public function __construct(
        array $ctorArgs = [],
        private readonly Configuration $configuration = new Configuration(),
    ) {
        $this->application = new DevToolsApplication($this->configuration);
    }

    /**
     * @return array<string, string>
     */
    public function getCapabilities(): array
    {
        return [
            CommandProvider::class => self::class,
        ];
    }

    /**
     * @return BaseCommand[]
     */
    public function getCommands(): array
    {
        $skipCommands = ['help', 'list', '_complete', 'completion'];
        $commands = [];

        foreach ($this->application->all() as $name => $command) {
            if (!in_array($name, $skipCommands)) {
                assert($command instanceof Command);
                $commands[(string) $command->getName()] = new ComposerCommand($command);
            }
        }

        return array_values($commands);
    }

    public function activate(Composer $composer, IOInterface $io, Filesystem $filesystem = new Filesystem()): void
    {
        self::setupBuildDirectory(filesystem: $filesystem, io: $io);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}
