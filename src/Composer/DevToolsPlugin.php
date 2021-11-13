<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * ramsey/devtools-lib is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Installer\PackageEvent;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Ramsey\Dev\Tools\Composer\Command\AnalyzeCommand;
use Ramsey\Dev\Tools\Composer\Command\AnalyzePhpStanCommand;
use Ramsey\Dev\Tools\Composer\Command\AnalyzePsalmCommand;
use Ramsey\Dev\Tools\Composer\Command\BaseCommand;
use Ramsey\Dev\Tools\Composer\Command\BuildCleanCacheCommand;
use Ramsey\Dev\Tools\Composer\Command\BuildCleanCommand;
use Ramsey\Dev\Tools\Composer\Command\BuildCleanCoverageCommand;
use Ramsey\Dev\Tools\Composer\Command\Configuration;
use Ramsey\Dev\Tools\Composer\Command\KeepAChangelogCommand;
use Ramsey\Dev\Tools\Composer\Command\LicenseCheckerCommand;
use Ramsey\Dev\Tools\Composer\Command\LintCommand;
use Ramsey\Dev\Tools\Composer\Command\LintFixCommand;
use Ramsey\Dev\Tools\Composer\Command\LintPdsCommand;
use Ramsey\Dev\Tools\Composer\Command\LintStyleCommand;
use Ramsey\Dev\Tools\Composer\Command\LintSyntaxCommand;
use Ramsey\Dev\Tools\Composer\Command\PreCommitCommand;
use Ramsey\Dev\Tools\Composer\Command\TestAllCommand;
use Ramsey\Dev\Tools\Composer\Command\TestCoverageCiCommand;
use Ramsey\Dev\Tools\Composer\Command\TestCoverageHtmlCommand;
use Ramsey\Dev\Tools\Composer\Command\TestUnitCommand;
use Ramsey\Dev\Tools\Filesystem\Filesystem;

use function dirname;
use function realpath;

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

    private static Composer $composer;

    private string $repoRoot;

    public static function setupBuildDirectory(PackageEvent $event, ?Filesystem $filesystem = null): void
    {
        $filesystem = $filesystem ?? new Filesystem();

        if (!$filesystem->exists('./build')) {
            $event->getIO()->write('<comment>Creating build directory</comment>');
            $filesystem->mkdir('./build');
            $filesystem->appendToFile('./build/.gitignore', self::BUILD_GITIGNORE_BASE);
        }

        if (!$filesystem->exists('./build/cache')) {
            $event->getIO()->write('<comment>Creating build/cache directory</comment>');
            $filesystem->mkdir('./build/cache');
            $filesystem->touch('./build/cache/.gitkeep');
            $filesystem->appendToFile('./build/.gitignore', self::BUILD_GITIGNORE_CACHE);
        }

        if (!$filesystem->exists('./build/coverage')) {
            $event->getIO()->write('<comment>Creating build/coverage directory</comment>');
            $filesystem->mkdir('./build/coverage');
            $filesystem->touch('./build/coverage/.gitkeep');
            $filesystem->appendToFile('./build/.gitignore', self::BUILD_GITIGNORE_COVERAGE);
        }
    }

    public function __construct()
    {
        $composerFile = Factory::getComposerFile();

        $this->repoRoot = (string) realpath(dirname($composerFile));
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
        $config = new Configuration(self::$composer, $this->getCommandPrefix(), $this->repoRoot);

        /** @psalm-suppress DeprecatedClass */
        return [
            new AnalyzeCommand($config),
            new AnalyzePhpStanCommand($config),
            new AnalyzePsalmCommand($config),
            new BuildCleanCacheCommand($config),
            new BuildCleanCommand($config),
            new BuildCleanCoverageCommand($config),
            new KeepAChangelogCommand($config),
            new LicenseCheckerCommand($config),
            new LintCommand($config),
            new LintFixCommand($config),
            new LintPdsCommand($config),
            new LintStyleCommand($config),
            new LintSyntaxCommand($config),
            new PreCommitCommand($config),
            new TestAllCommand($config),
            new TestCoverageCiCommand($config),
            new TestCoverageHtmlCommand($config),
            new TestUnitCommand($config),
        ];
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        self::$composer = $composer;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * Use extra.command-prefix, if available, but extra.ramsey/devtools.command-prefix
     * takes precedence over extra.command-prefix.
     */
    private function getCommandPrefix(): string
    {
        /** @var array{command-prefix?: string, "ramsey/devtools"?: array{command-prefix?: string}} $extra */
        $extra = self::$composer->getPackage()->getExtra();

        return $extra['ramsey/devtools']['command-prefix'] ?? $extra['command-prefix'] ?? '';
    }
}
