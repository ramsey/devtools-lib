<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

use Composer\Composer;
use Composer\Factory as ComposerFactory;
use Composer\IO\ConsoleIO;
use Composer\Util\Platform;
use RuntimeException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use function getcwd;
use function realpath;

class Factory
{
    public function __construct(
        public readonly Filesystem $filesystem = new Filesystem(),
    ) {
    }

    public function getComposer(
        ?InputInterface $input = null,
        ?OutputInterface $output = null,
        ?HelperSet $helperSet = null,
    ): Composer {
        return ComposerFactory::create(
            io: new ConsoleIO(
                $input ?? new ArgvInput(),
                $output ?? new ConsoleOutput(),
                $helperSet ?? new HelperSet(),
            ),
            config: $this->findComposerFile(),
            disablePlugins: true,
            disableScripts: true,
        );
    }

    /**
     * @codeCoverageIgnore
     */
    private function findComposerFile(): string
    {
        $composerFile = ComposerFactory::getComposerFile();

        if ($this->filesystem->exists($composerFile)) {
            return (string) realpath(Path::canonicalize($composerFile));
        }

        $dir = Path::getDirectory(getcwd() ?: '');
        $home = $this->getHomeDirectory();

        // Search through the parent directories until we
        // reach the home directory or top of file system.
        while (Path::getDirectory($dir) !== $dir && $dir !== $home) {
            if ($this->filesystem->exists($dir . '/' . $composerFile)) {
                break;
            }
            $dir = Path::getDirectory($dir);
        }

        return (string) realpath(Path::canonicalize($dir . '/' . $composerFile));
    }

    /**
     * @codeCoverageIgnore
     */
    private function getHomeDirectory(): string
    {
        if (Platform::getEnv('HOME') !== false) {
            return Path::canonicalize((string) Platform::getEnv('HOME'));
        }

        if (Platform::getEnv('USERPROFILE') !== false) {
            return Path::canonicalize((string) Platform::getEnv('USERPROFILE'));
        }

        if (Platform::getEnv('HOMEDRIVE') !== false && Platform::getEnv('HOMEPATH') !== false) {
            return Path::canonicalize((string) Platform::getEnv('HOMEDRIVE') . (string) Platform::getEnv('HOMEPATH'));
        }

        throw new RuntimeException('Cannot find the home directory path');
    }
}
