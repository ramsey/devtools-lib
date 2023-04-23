<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command;

use Composer\Composer;
use Composer\EventDispatcher\EventDispatcher;
use Ramsey\Dev\Tools\Command\Command;
use Ramsey\Dev\Tools\Composer\Factory;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CommandTest extends TestCase
{
    public function testSetHelp(): void
    {
        $helpTextToSet = <<<'EOD'
            <info>Lorem ipsum dolor sit amet</info>, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
            labore et dolore magna aliqua.

            Eget nulla facilisi etiam dignissim.
             Fermentum dui faucibus in ornare quam viverra orci sagittis.

              <code>This is some code</code>

              * Foo
              * Bar
              * Baz

            Eget nulla facilisi etiam dignissim.
            Fermentum dui faucibus in ornare quam viverra orci sagittis.

            Quisque non tellus orci ac <code>auctor augue</code> mauris augue. Tincidunt nunc pulvinar sapien et ligula
            ullamcorper malesuada proin. Molestie ac feugiat sed lectus vestibulum mattis. Ultricies mi quis hendrerit
            dolor magna. Est ultricies integer <link>quis</link> auctor elit sed vulputate. Vitae tortor condimentum
            lacinia quis vel eros donec ac odio. Egestas tellus <href=https://example.com>rutrum tellus pellentesque</>.
            EOD;

        $expectedHelp = <<<'EOD'
            <info>Lorem ipsum dolor sit amet</info>, consectetur adipiscing elit, sed do eiusmod tempor
            incididunt ut labore et dolore magna aliqua.

            Eget nulla facilisi etiam dignissim.
             Fermentum dui faucibus in ornare quam viverra orci sagittis.

              <code>This is some code</code>

              * Foo
              * Bar
              * Baz

            Eget nulla facilisi etiam dignissim. Fermentum dui faucibus in ornare quam
            viverra orci sagittis.

            Quisque non tellus orci ac <code>auctor augue</code> mauris augue. Tincidunt nunc pulvinar
            sapien et ligula ullamcorper malesuada proin. Molestie ac feugiat sed lectus
            vestibulum mattis. Ultricies mi quis hendrerit dolor magna. Est ultricies
            integer <link>quis</link> auctor elit sed vulputate. Vitae tortor condimentum lacinia quis
            vel eros donec ac odio. Egestas tellus <href=https://example.com>rutrum tellus pellentesque</>.
            EOD;

        $command = new class (new Configuration()) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                return SymfonyCommand::SUCCESS;
            }
        };

        $command->setHelp($helpTextToSet);

        $this->assertSame($expectedHelp, $command->getHelp());
    }

    public function testComposerConfigurationWithScriptAsArray(): void
    {
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->expects('setRunScripts')->with(true);
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Hello"');
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "World"');
        $eventDispatcher->expects('dispatchScript')->with('my-command')->andReturns(9);

        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getEventDispatcher' => $eventDispatcher,
            'getPackage->getExtra' => [
                'devtools' => ['commands' => ['my-command' => ['script' => 'should not run']]],
                'ramsey/devtools' => [
                    'commands' => [
                        'my-command' => [
                            'script' => [
                                'echo "Hello"',
                                'echo "World"',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $command = new #[AsCommand(name: 'my-command')] class (
            new Configuration(composerFactory: $composerFactory),
        ) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                // We'll use this value to assert that doExecute() was called.
                return 3;
            }
        };

        $this->assertSame(9 + 3, $command->run(new StringInput(''), new NullOutput()));
    }

    public function testComposerConfigurationWithScriptAsString(): void
    {
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->expects('setRunScripts')->with(true);
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Hello World"');
        $eventDispatcher->expects('dispatchScript')->with('my-command')->andReturns(4);

        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getEventDispatcher' => $eventDispatcher,
            'getPackage->getExtra' => [
                'devtools' => ['commands' => ['my-command' => ['script' => 'should not run']]],
                'ramsey/devtools' => [
                    'commands' => [
                        'my-command' => [
                            'script' => 'echo "Hello World"',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $command = new #[AsCommand(name: 'my-command')] class (
            new Configuration(composerFactory: $composerFactory),
        ) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                // We'll use this value to assert that doExecute() was called.
                return 7;
            }
        };

        $this->assertSame(4 + 7, $command->run(new StringInput(''), new NullOutput()));
    }

    public function testComposerConfigurationWithScriptOverride(): void
    {
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->expects('setRunScripts')->with(true);
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Hello World"');
        $eventDispatcher->expects('dispatchScript')->with('my-command')->andReturns(17);

        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getEventDispatcher' => $eventDispatcher,
            'getPackage->getExtra' => [
                'devtools' => ['commands' => ['my-command' => ['script' => 'should not run']]],
                'ramsey/devtools' => [
                    'commands' => [
                        'my-command' => [
                            'override' => true,
                            'script' => 'echo "Hello World"',
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $command = new #[AsCommand(name: 'my-command')] class (
            new Configuration(composerFactory: $composerFactory),
        ) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                // We'll use this value to assert that doExecute() was NOT called.
                // That is, this value should not be included in the result.
                return 9;
            }
        };

        $this->assertSame(17 + 0, $command->run(new StringInput(''), new NullOutput()));
    }

    public function testComposerConfigurationWithNoExtraScripts(): void
    {
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->expects('setRunScripts')->with(true);
        $eventDispatcher->expects('addListener')->never();
        $eventDispatcher->expects('dispatchScript')->with('my-command')->andReturns(0);

        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getEventDispatcher' => $eventDispatcher,
            'getPackage->getExtra' => [],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $command = new #[AsCommand(name: 'my-command')] class (
            new Configuration(composerFactory: $composerFactory),
        ) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                // We'll use this value to assert that doExecute() was called.
                return 9;
            }
        };

        $this->assertSame(9, $command->run(new StringInput(''), new NullOutput()));
    }

    public function testComposerConfigurationFromDevtoolsPropertyWithScriptAsArray(): void
    {
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->expects('setRunScripts')->with(true);
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Foo"');
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Bar"');
        $eventDispatcher->expects('dispatchScript')->with('my-command')->andReturns(13);

        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getEventDispatcher' => $eventDispatcher,
            'getPackage->getExtra' => [
                'devtools' => [
                    'commands' => [
                        'my-command' => [
                            'script' => [
                                'echo "Foo"',
                                'echo "Bar"',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $command = new #[AsCommand(name: 'my-command')] class (
            new Configuration(composerFactory: $composerFactory),
        ) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                // We'll use this value to assert that doExecute() was called.
                return 6;
            }
        };

        $this->assertSame(13 + 6, $command->run(new StringInput(''), new NullOutput()));
    }

    public function testComposerConfigurationFromCustomComposerExtraPropertyWithScriptAsArray(): void
    {
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->expects('setRunScripts')->with(true);
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Baz"');
        $eventDispatcher->expects('addListener')->with('my-command', 'echo "Qux"');
        $eventDispatcher->expects('dispatchScript')->with('my-command')->andReturns(9);

        $composer = $this->mockery(Composer::class, [
            'getConfig->get' => '',
            'getEventDispatcher' => $eventDispatcher,
            'getPackage->getExtra' => [
                'devtools' => ['commands' => ['my-command' => ['script' => 'should not run']]],
                'ramsey/devtools' => ['commands' => ['my-command' => ['script' => 'should also not run']]],
                'my-tools' => [
                    'commands' => [
                        'my-command' => [
                            'script' => [
                                'echo "Baz"',
                                'echo "Qux"',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $composerFactory = $this->mockery(Factory::class, [
            'getComposer' => $composer,
        ]);

        $command = new #[AsCommand(name: 'my-command')] class (
            new Configuration(composerFactory: $composerFactory, composerExtraProperty: 'my-tools'),
        ) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                // We'll use this value to assert that doExecute() was called.
                return 3;
            }
        };

        $this->assertSame(9 + 3, $command->run(new StringInput(''), new NullOutput()));
    }
}