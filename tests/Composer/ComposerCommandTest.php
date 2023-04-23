<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer;

use PHPUnit\Framework\Attributes\TestWith;
use Ramsey\Dev\Tools\Composer\ComposerCommand;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class ComposerCommandTest extends TestCase
{
    public function testConfiguringCommand(): void
    {
        $definition = new InputDefinition();

        $symfonyCommand = $this->mockery(Command::class, [
            'getName' => 'foobar',
            'getAliases' => ['foo', 'bar'],
            'getDescription' => 'A test description',
            'getHelp' => 'Some help to show you how to use <code>foobar</code>',
            'getDefinition' => $definition,
            'isHidden' => true,
            'getUsages' => ['foobar baz', 'foobar qux', 'quux'],
        ]);

        $symfonyCommand->expects('setName')->with('wat:foobar');

        $composerCommand = new ComposerCommand($symfonyCommand, 'wat');

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
        $symfonyCommand = new Command('foobar');
        $symfonyCommand->setCode(fn (): int => Command::SUCCESS);
        $composerCommand = new ComposerCommand($symfonyCommand, '');

        $input = new StringInput('');
        $output = new NullOutput();

        $this->assertSame(Command::SUCCESS, $composerCommand->run($input, $output));
    }

    #[TestWith(['', 'foobar'])]
    #[TestWith([':', 'foobar'])]
    #[TestWith(['abc:', 'abc:foobar'])]
    #[TestWith(['def', 'def:foobar'])]
    #[TestWith(['ghi::::::', 'ghi:foobar'])]
    public function testCommandPrefixes(string $prefix, string $expectedName): void
    {
        $definition = new InputDefinition();

        $symfonyCommand = $this->mockery(Command::class, [
            'getName' => 'foobar',
            'getAliases' => [],
            'getDescription' => '',
            'getHelp' => '',
            'getDefinition' => $definition,
            'isHidden' => true,
            'getUsages' => ['foobar baz', 'foobar qux', 'quux'],
        ]);
        $symfonyCommand->expects('setName')->with($expectedName);

        $composerCommand = new ComposerCommand($symfonyCommand, $prefix);

        $this->assertSame($expectedName, $composerCommand->getName());
        $this->assertSame(
            ["$expectedName baz", "$expectedName qux", "$expectedName quux"],
            $composerCommand->getUsages(),
        );
    }
}
