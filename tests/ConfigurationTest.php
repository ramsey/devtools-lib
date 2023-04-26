<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools;

use Composer\Composer;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\Process\ProcessFactory;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Filesystem\Filesystem;

use function dirname;
use function getenv;
use function strtr;

use const DIRECTORY_SEPARATOR;
use const PATH_SEPARATOR;

class ConfigurationTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testConstructorSetsDefaultValues(): void
    {
        $expectedProjectRoot = dirname(__DIR__);
        $expectedBinDir = $expectedProjectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin';
        $currentPath = getenv('PATH');

        $configuration = new Configuration();
        $newPath = getenv('PATH');

        $this->assertInstanceOf(Factory::class, $configuration->composerFactory);
        $this->assertInstanceOf(Composer::class, $configuration->composer);
        $this->assertInstanceOf(ProcessFactory::class, $configuration->processFactory);
        $this->assertInstanceOf(Filesystem::class, $configuration->filesystem);
        $this->assertSame($expectedProjectRoot, $configuration->projectRoot);
        $this->assertSame($expectedBinDir, $configuration->composerBinDir);
        $this->assertNotSame($currentPath, $newPath, "currentPath: $currentPath, newPath: $newPath");
        $this->assertSame($newPath, $configuration->execPath);
        $this->assertMatchesRegularExpression(
            '/^' . strtr($expectedBinDir, ['/' => '\/', '\\' => '\\\\']) . PATH_SEPARATOR . '.*/',
            $configuration->execPath,
        );
        $this->assertSame('ramsey/devtools', $configuration->composerExtraProperty);
        $this->assertSame('dev', $configuration->composerDefaultCommandPrefix);
    }
}
