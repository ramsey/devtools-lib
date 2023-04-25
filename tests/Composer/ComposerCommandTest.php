<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use Ramsey\Dev\Tools\Command\Command;
use Ramsey\Dev\Tools\Composer\ComposerCommand;
use Ramsey\Dev\Tools\Composer\ExtraConfiguration;
use Ramsey\Dev\Tools\Configuration;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerCommandTest extends TestCase
{
    public function testConfiguringCommand(): void
    {
        $definition = new InputDefinition();

        $command = $this->mockery(Command::class, [
            'getName' => 'foobar',
            'getAliases' => ['foo', 'bar'],
            'getDescription' => 'A test description',
            'getHelp' => 'Some help to show you how to use <code>foobar</code>',
            'getDefinition' => $definition,
            'isHidden' => true,
            'getUsages' => ['foobar baz', 'foobar qux', 'quux'],
            'getExtra' => new ExtraConfiguration(commandName: 'foobar', commandPrefix: 'wat'),
        ]);

        $composerCommand = new ComposerCommand($command);

        $this->assertSame('wat:foobar', $composerCommand->getName());
        $this->assertSame(['foo', 'bar'], $composerCommand->getAliases());
        $this->assertSame('A test description', $composerCommand->getDescription());
        $this->assertSame('Some help to show you how to use <code>wat:foobar</code>', $composerCommand->getHelp());
        $this->assertSame($definition, $composerCommand->getDefinition());
        $this->assertTrue($composerCommand->isHidden());
        $this->assertSame(['wat:foobar baz', 'wat:foobar qux', 'wat:foobar quux'], $composerCommand->getUsages());
    }

    public function testExecute(): void
    {
        $command = new #[AsCommand(name: 'foo')] class (new Configuration()) extends Command {
            protected function doExecute(InputInterface $input, OutputInterface $output): int
            {
                return 1234;
            }
        };

        $composerCommand = new ComposerCommand($command);

        $input = new StringInput('');
        $output = new NullOutput();

        $this->assertSame(1234, $composerCommand->run($input, $output));
    }
}
