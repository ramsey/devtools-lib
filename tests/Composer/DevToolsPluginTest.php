<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Installer\PackageEvent;
use Composer\Plugin\Capability\CommandProvider;
use Mockery;
use Mockery\MockInterface;
use Ramsey\Dev\Tools\Composer\Command\BaseCommand;
use Ramsey\Dev\Tools\Composer\Command\CaptainHookInstallCommand;
use Ramsey\Dev\Tools\Composer\DevToolsPlugin;
use Ramsey\Dev\Tools\Filesystem\Filesystem;
use Ramsey\Dev\Tools\TestCase;

use function count;

class DevToolsPluginTest extends TestCase
{
    public function testGetCapabilities(): void
    {
        $plugin = new DevToolsPlugin();

        $this->assertSame(
            [
                CommandProvider::class => DevToolsPlugin::class,
            ],
            $plugin->getCapabilities(),
        );
    }

    public function testGetCommands(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);
        $config->allows()->get('bin-dir')->andReturn('/path/to/bin-dir');

        /** @var EventDispatcher & MockInterface $eventDispatcher */
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->shouldReceive('addListener');
        $eventDispatcher->shouldReceive('dispatchScript')->andReturn(0);

        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'command-prefix' => 'foo',
            ],
            'getConfig' => $config,
            'getEventDispatcher' => $eventDispatcher,
        ]);

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        $pluginActivated = new DevToolsPlugin();
        $pluginActivated->activate($composer, $io);

        // This will test that our $composer instance was set
        // statically on the class.
        $pluginToUseForCommands = new DevToolsPlugin();
        $commands = $pluginToUseForCommands->getCommands();

        $this->assertContainsOnlyInstancesOf(BaseCommand::class, $commands);
        $this->assertGreaterThan(0, count($commands));
        $this->assertSame('foo:', $commands[0]->getPrefix());
        $this->assertSame('/path/to/bin-dir', $commands[0]->getBinDir());
    }

    public function testGetCommandsWithRamseyDevtoolsCommandPrefixProperty(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);
        $config->allows()->get('bin-dir')->andReturn('/path/to/bin-dir');

        /** @var EventDispatcher & MockInterface $eventDispatcher */
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->shouldReceive('addListener');
        $eventDispatcher->shouldReceive('dispatchScript')->andReturn(0);

        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'command-prefix' => 'foo',
                'ramsey/devtools' => [
                    'command-prefix' => 'bar',
                ],
            ],
            'getConfig' => $config,
            'getEventDispatcher' => $eventDispatcher,
        ]);

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        $pluginActivated = new DevToolsPlugin();
        $pluginActivated->activate($composer, $io);

        // This will test that our $composer instance was set
        // statically on the class.
        $pluginToUseForCommands = new DevToolsPlugin();
        $commands = $pluginToUseForCommands->getCommands();

        $this->assertContainsOnlyInstancesOf(BaseCommand::class, $commands);
        $this->assertGreaterThan(0, count($commands));
        $this->assertSame('bar:', $commands[0]->getPrefix());
        $this->assertSame('/path/to/bin-dir', $commands[0]->getBinDir());
    }

    public function testActivate(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = Mockery::mock(Composer::class);

        /** @var IOInterface & MockInterface $io */
        $io = Mockery::spy(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->activate($composer, $io);

        $composer->shouldNotHaveBeenCalled();
        $io->shouldNotHaveBeenCalled();
    }

    public function testDeactivate(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = Mockery::mock(Composer::class);

        /** @var IOInterface & MockInterface $io */
        $io = Mockery::spy(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->deactivate($composer, $io);

        $composer->shouldNotHaveBeenCalled();
        $io->shouldNotHaveBeenCalled();
    }

    public function testUninstall(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = Mockery::mock(Composer::class);

        /** @var IOInterface & MockInterface $io */
        $io = Mockery::spy(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->uninstall($composer, $io);

        $composer->shouldNotHaveBeenCalled();
        $io->shouldNotHaveBeenCalled();
    }

    public function testGetCaptainHookInstallCommand(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);
        $config->allows()->get('bin-dir')->andReturn('/path/to/bin-dir');

        /** @var EventDispatcher & MockInterface $eventDispatcher */
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->shouldReceive('addListener');
        $eventDispatcher->shouldReceive('dispatchScript')->andReturn(0);

        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [],
            'getConfig' => $config,
            'getEventDispatcher' => $eventDispatcher,
        ]);

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        $pluginActivated = new DevToolsPlugin();
        $pluginActivated->activate($composer, $io);

        // This will test that our $composer instance was set
        // statically on the class.
        $pluginToUseForExecution = new DevToolsPlugin();

        $this->assertInstanceOf(
            CaptainHookInstallCommand::class,
            $pluginToUseForExecution->getCaptainHookInstallCommand(),
        );
    }

    public function testSetupBuildDirectory(): void
    {
        $io = $this->mockery(IOInterface::class);
        $io->expects()->write('<comment>Creating build directory</comment>');
        $io->expects()->write('<comment>Creating build/cache directory</comment>');
        $io->expects()->write('<comment>Creating build/coverage directory</comment>');

        $event = $this->mockery(PackageEvent::class, [
            'getIO' => $io,
        ]);

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem->expects()->exists('./build')->andReturnFalse();
        $filesystem->expects()->mkdir('./build');
        $filesystem->expects()->appendToFile('./build/.gitignore', "\n*\n!.gitignore\n");

        $filesystem->expects()->exists('./build/cache')->andReturnFalse();
        $filesystem->expects()->mkdir('./build/cache');
        $filesystem->expects()->touch('./build/cache/.gitkeep');
        $filesystem->expects()->appendToFile('./build/.gitignore', "\ncache/*\n!cache\n!cache/.gitkeep\n");

        $filesystem->expects()->exists('./build/coverage')->andReturnFalse();
        $filesystem->expects()->mkdir('./build/coverage');
        $filesystem->expects()->touch('./build/coverage/.gitkeep');
        $filesystem->expects()->appendToFile('./build/.gitignore', "\ncoverage/*\n!coverage\n!coverage/.gitkeep\n");

        DevToolsPlugin::setupBuildDirectory($event, $filesystem);
    }

    public function testSetupBuildDirectoryForCacheDirectory(): void
    {
        $io = $this->mockery(IOInterface::class);
        $io->expects()->write('<comment>Creating build/cache directory</comment>');

        $event = $this->mockery(PackageEvent::class, [
            'getIO' => $io,
        ]);

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem->expects()->exists('./build')->andReturnTrue();
        $filesystem->expects()->exists('./build/coverage')->andReturnTrue();

        $filesystem->expects()->exists('./build/cache')->andReturnFalse();
        $filesystem->expects()->mkdir('./build/cache');
        $filesystem->expects()->touch('./build/cache/.gitkeep');
        $filesystem->expects()->appendToFile('./build/.gitignore', "\ncache/*\n!cache\n!cache/.gitkeep\n");

        DevToolsPlugin::setupBuildDirectory($event, $filesystem);
    }

    public function testSetupBuildDirectoryForCoverageDirectory(): void
    {
        $io = $this->mockery(IOInterface::class);
        $io->expects()->write('<comment>Creating build/coverage directory</comment>');

        $event = $this->mockery(PackageEvent::class, [
            'getIO' => $io,
        ]);

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem->expects()->exists('./build')->andReturnTrue();
        $filesystem->expects()->exists('./build/cache')->andReturnTrue();

        $filesystem->expects()->exists('./build/coverage')->andReturnFalse();
        $filesystem->expects()->mkdir('./build/coverage');
        $filesystem->expects()->touch('./build/coverage/.gitkeep');
        $filesystem->expects()->appendToFile('./build/.gitignore', "\ncoverage/*\n!coverage\n!coverage/.gitkeep\n");

        DevToolsPlugin::setupBuildDirectory($event, $filesystem);
    }
}
