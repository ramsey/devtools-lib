<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\PackageEvent;
use Composer\Plugin\Capability\CommandProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Ramsey\Dev\Tools\Command\Command as DevToolsCommand;
use Ramsey\Dev\Tools\Composer\ComposerCommand;
use Ramsey\Dev\Tools\Composer\DevToolsPlugin;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Filesystem\Filesystem;

use function array_unique;

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

    /**
     * @param array{command-prefix?: string, "devtools"?: array{command-prefix?: string}, "ramsey/devtools"?: array{command-prefix?: string}} $extra
     */
    #[TestWith(['dev:', []])]
    #[TestWith(['foo:', ['command-prefix' => 'foo']])]
    #[TestWith(['foo:', ['command-prefix' => 'foo:']])]
    #[TestWith(['', ['command-prefix' => '']])]
    #[TestWith(['', ['command-prefix' => ':']])]
    #[TestWith(['bar:', ['command-prefix' => 'foo', 'devtools' => ['command-prefix' => 'bar']]])]
    #[TestWith(['bar:', ['command-prefix' => 'foo', 'devtools' => ['command-prefix' => 'bar:']]])]
    #[TestWith(['', ['command-prefix' => 'foo', 'devtools' => ['command-prefix' => '']]])]
    #[TestWith(['', ['command-prefix' => 'foo', 'devtools' => ['command-prefix' => ':']]])]
    #[TestWith([
        'baz:',
        [
            'command-prefix' => 'foo',
            'devtools' => ['command-prefix' => 'bar'],
            'ramsey/devtools' => ['command-prefix' => 'baz'],
        ],
    ])]
    #[TestWith([
        'baz:',
        [
            'command-prefix' => 'foo',
            'devtools' => ['command-prefix' => 'bar'],
            'ramsey/devtools' => ['command-prefix' => 'baz:'],
        ],
    ])]
    #[TestWith([
        '',
        [
            'command-prefix' => 'foo',
            'devtools' => ['command-prefix' => 'bar'],
            'ramsey/devtools' => ['command-prefix' => ''],
        ],
    ])]
    #[TestWith([
        '',
        [
            'command-prefix' => 'foo',
            'devtools' => ['command-prefix' => 'bar'],
            'ramsey/devtools' => ['command-prefix' => ':'],
        ],
    ])]
    public function testGetCommands(string $expectedPrefix, array $extra): void
    {
        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getPackage->getExtra' => $extra,
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $plugin = new DevToolsPlugin([], new Configuration(composerFactory: $composerFactory));
        $commands = $plugin->getCommands();
        $commandNames = [];

        foreach ($commands as $command) {
            $this->assertInstanceOf(ComposerCommand::class, $command);
            $this->assertInstanceOf(DevToolsCommand::class, $command->wrappedCommand);
            if ($expectedPrefix === '') {
                $this->assertSame($command->originalName, $command->getName());
            } else {
                $this->assertStringStartsWith($expectedPrefix, (string) $command->getName());
            }
            $commandNames[] = (string) $command->getName();
        }

        // We should have the exact same list after removing duplicates.
        // In other words, there shouldn't be any duplicate commands.
        $deDupedCommandNames = array_unique($commandNames);
        $this->assertSame($commandNames, $deDupedCommandNames);
    }

    public function testActivate(): void
    {
        $composer = $this->mockery(Composer::class);

        $io = $this->mockery(IOInterface::class);
        $io->expects()->write('<comment>Creating build directory</comment>');
        $io->expects()->write('<comment>Creating build/cache directory</comment>');
        $io->expects()->write('<comment>Creating build/coverage directory</comment>');

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

        $plugin = new DevToolsPlugin();
        $plugin->activate($composer, $io, $filesystem);
    }

    public function testDeactivate(): void
    {
        $composer = $this->mockery(Composer::class);
        $io = $this->mockery(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->deactivate($composer, $io);

        // Without an assertion, PHPUnit complains this is a risky test. The
        // real assertion should be that nothing is called on $composer and $io,
        // but Mockery does not provide that kind of assertion. If anything is
        // called on them, then Mockery will throw an exception because the call
        // is unexpected.
        $this->assertTrue(true);
    }

    public function testUninstall(): void
    {
        $composer = $this->mockery(Composer::class);
        $io = $this->mockery(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->uninstall($composer, $io);

        // Without an assertion, PHPUnit complains this is a risky test. The
        // real assertion should be that nothing is called on $composer and $io,
        // but Mockery does not provide that kind of assertion. If anything is
        // called on them, then Mockery will throw an exception because the call
        // is unexpected.
        $this->assertTrue(true);
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
