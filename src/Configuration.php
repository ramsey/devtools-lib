<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools;

use Composer\Composer;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use function assert;
use function getenv;
use function is_string;
use function putenv;
use function realpath;

use const PATH_SEPARATOR;

final class Configuration
{
    public const EXTRA_PROPERTY = 'ramsey/devtools';
    public const DEFAULT_COMMAND_PREFIX = 'dev';

    public readonly Composer $composer;
    public readonly string $composerBinDir;
    public readonly string $execPath;
    public readonly string $projectRoot;

    /**
     * @param string $composerExtraProperty The property to look for within the
     *     Composer "extra" data that defines additional devtools configuration.
     *     Defaults to {@see self::EXTRA_PROPERTY}.
     */
    public function __construct(
        public readonly Factory $composerFactory = new Factory(),
        public readonly ProcessFactory $processFactory = new ProcessFactory(),
        public readonly Filesystem $filesystem = new Filesystem(),
        public readonly string $composerExtraProperty = self::EXTRA_PROPERTY,
        public readonly string $composerDefaultCommandPrefix = self::DEFAULT_COMMAND_PREFIX,
    ) {
        $this->composer = $this->composerFactory->getComposer();
        $this->composerBinDir = $this->getComposerBinDir();
        $this->execPath = $this->setExecPath();
        $this->projectRoot = $this->getProjectRoot();
    }

    private function getComposerBinDir(): string
    {
        $binDir = $this->composer->getConfig()->get('bin-dir');
        assert(is_string($binDir));

        return (string) realpath($binDir);
    }

    private function getProjectRoot(): string
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        assert(is_string($vendorDir));

        return (string) realpath(Path::getDirectory($vendorDir));
    }

    private function setExecPath(): string
    {
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable
        $currentPath = $_ENV['PATH'] ?? $_SERVER['PATH'] ?? (string) getenv('PATH');
        $newPath = $currentPath;

        if ($this->composerBinDir) {
            $newPath = $this->composerBinDir . ($newPath ? PATH_SEPARATOR : '') . $newPath;
        }

        putenv("PATH=$newPath");

        return $newPath;
    }
}
