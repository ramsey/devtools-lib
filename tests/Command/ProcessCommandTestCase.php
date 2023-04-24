<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command;

use Ramsey\Dev\Tools\Command\ProcessCommand;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

use function strlen;

abstract class ProcessCommandTestCase extends TestCase
{
    /**
     * The system-under-test (SUT) command.
     */
    protected ProcessCommand $sutCommand;

    /**
     * Returns the expected description of the command.
     */
    abstract protected function getExpectedDescription(): string;

    /**
     * Since we're testing a process command, it must have an executable we
     * intend to execute. This returns the expected name of the executable,
     * without the path (i.e., "phpunit", "psalm", "phpstan", etc.).
     */
    abstract protected function getExpectedExecutableName(): string;

    /**
     * Since we're testing a process command, it must have an executable that
     * comes from some installable package. If we can't find the executable, we
     * throw an exception with a helpful message that explains how to install
     * the software. This returns the expected installation message.
     */
    abstract protected function getExpectedInstallationMessage(): string;

    /**
     * Returns the expected name of the command (i.e., "test:unit").
     */
    abstract protected function getExpectedName(): string;

    /**
     * Returns an array describing tests for the getProcessCommand() method.
     * Each item in the array is a test with the argvInput to use and the
     * array we expect to get when calling getProcessCommand().
     *
     * @return array<array{argvInput: string[], expected: string[]}>
     */
    abstract protected function getProcessCommandTests(): array;

    /**
     * Returns a new system-under-test (SUT) command.
     */
    abstract protected function getSutCommand(): ProcessCommand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sutCommand = $this->getSutCommand();
    }

    /**
     * Returns an array of the expected aliases for the command.
     *
     * @return string[]
     */
    protected function getExpectedAliases(): array
    {
        return [];
    }

    /**
     * Returns an array describing the expected definition of the command.
     *
     * @return array<string, array{name: string, description: string}>
     */
    protected function getExpectedDefinition(): array
    {
        return [];
    }

    /**
     * Returns an array of the expected usages printed in the help text for the
     * command.
     *
     * @return string[]
     */
    protected function getExpectedUsages(): array
    {
        return [];
    }

    public function testDefinition(): void
    {
        $definition = $this->sutCommand->getDefinition();

        foreach ($this->getExpectedDefinition() as $argument => $properties) {
            $this->assertArrayHasKey($argument, $definition->getArguments());
            $this->assertSame($properties['name'], $definition->getArguments()[$argument]->getName());
            $this->assertSame($properties['description'], $definition->getArguments()[$argument]->getDescription());
        }

        if ($this->getExpectedDefinition() === []) {
            $this->assertSame([], $definition->getArguments());
        }
    }

    public function testGetName(): void
    {
        $this->assertSame($this->getExpectedName(), $this->sutCommand->getName());
    }

    public function testGetAliases(): void
    {
        $this->assertSame($this->getExpectedAliases(), $this->sutCommand->getAliases());
    }

    public function testGetDescription(): void
    {
        $this->assertSame($this->getExpectedDescription(), $this->sutCommand->getDescription());
    }

    public function testGetExecutableName(): void
    {
        $this->assertSame($this->getExpectedExecutableName(), $this->sutCommand->getExecutableName());
    }

    public function testGetExecutablePath(): void
    {
        $this->assertGreaterThan(0, strlen((string) $this->sutCommand->getExecutablePath()));
    }

    public function testGetHelp(): void
    {
        $this->assertNotEmpty($this->sutCommand->getHelp());
    }

    public function testGetPackageName(): void
    {
        $this->assertSame($this->getExpectedInstallationMessage(), $this->sutCommand->getInstallationMessage());
    }

    public function testGetProcessCommand(): void
    {
        $output = $this->mockery(OutputInterface::class);

        foreach ($this->getProcessCommandTests() as $test) {
            $input = new ArgvInput($test['argvInput'], $this->sutCommand->getDefinition());

            $this->assertSame(
                $test['expected'],
                $this->sutCommand->getProcessCommand($input, $output),
            );
        }
    }

    public function testUsages(): void
    {
        $this->assertSame($this->getExpectedUsages(), $this->sutCommand->getUsages());
    }
}
