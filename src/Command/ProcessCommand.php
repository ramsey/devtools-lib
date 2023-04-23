<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Closure;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;

use function filter_var;
use function preg_replace;
use function sprintf;
use function str_starts_with;
use function trim;

use const FILTER_VALIDATE_FLOAT;

abstract class ProcessCommand extends Command
{
    private const NOT_FOUND_MESSAGE = <<<'EOD'
        Unable to find %s in your PATH. Perhaps it's not installed?

        %s
        EOD;

    public bool $disableTty = false;
    private string | false | null $executablePath = false;

    /**
     * Returns the short name of the executable program used by this command.
     *
     * This short name is used to find the full path to the command, using the
     * current environment PATH.
     *
     * @see self::getExecutablePath()
     */
    abstract public function getExecutableName(): string;

    /**
     * Returns a helpful message explaining how to install the missing executable.
     */
    abstract public function getInstallationMessage(): string;

    /**
     * Returns the full command to execute, formatted as an array of command
     * options and arguments, with the executable as the first element of the
     * array.
     *
     * @return string[]
     */
    abstract public function getProcessCommand(InputInterface $input, OutputInterface $output): array;

    public function setHelp(string $help): static
    {
        $executable = $this->getExecutablePath();
        $name = $this->getExecutableName();

        $programInfo = "\n\n---\n\nThis command uses $name. On your system, $name is ";

        if ($executable === null) {
            $programInfo .= '<error>not installed</error>.';
            $programInfo .= "\n\n";
            $programInfo .= $this->getInstallationMessage();
        } else {
            if (str_starts_with((string) $this->getExecutablePath(), $this->configuration->projectRoot)) {
                $path = './'
                    . Path::makeRelative((string) $this->getExecutablePath(), $this->configuration->projectRoot);
            } else {
                $path = (string) $this->getExecutablePath();
            }
            $programInfo .= "located at: <file>$path</file>";
        }

        /** @var $this */
        return parent::setHelp($help . $programInfo);
    }

    /**
     * Returns the full path to the executable, based on the current environment
     * PATH.
     *
     * @return string | null The full path to the executable or `null`, if the executable is not found.
     */
    public function getExecutablePath(): ?string
    {
        if ($this->executablePath === false) {
            $this->executablePath = $this->configuration->processFactory->findExecutable(
                $this->getExecutableName(),
            );
        }

        return $this->executablePath;
    }

    /**
     * Returns `true` if the executable is in the current environment PATH.
     */
    public function hasExecutable(): bool
    {
        return $this->getExecutablePath() !== null;
    }

    protected function getProcessCallback(OutputInterface $output): Closure
    {
        return function (string $type, string $buffer) use ($output): void {
            if ($output instanceof ConsoleOutputInterface && $type === Process::ERR) {
                $output->getErrorOutput()->write($buffer);
            } else {
                $output->write($buffer);
            }
        };
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->hasExecutable()) {
            throw new ExecutableNotFound(trim(sprintf(
                self::NOT_FOUND_MESSAGE,
                $this->getExecutableName(),
                (string) preg_replace('/<[^>]+>|<\/[^>]*>/', '', $this->getInstallationMessage()),
            )));
        }

        $process = $this->configuration->processFactory->factory(
            $this->getProcessCommand($input, $output),
            $this->configuration->projectRoot,
            [
                'PATH' => $this->configuration->execPath,
                'RAMSEY_DEVTOOLS' => 'true',
            ],
        );

        $composerTimeout = filter_var(
            $this->configuration->composer->getConfig()->get('process-timeout'),
            FILTER_VALIDATE_FLOAT,
        );

        if ($composerTimeout !== false) {
            $process->setTimeout($composerTimeout);
        }

        if (!$this->disableTty && Process::isTtySupported()) {
            $process->setTty(true); // @codeCoverageIgnore
        }

        $process->start();

        return $process->wait($this->getProcessCallback($output));
    }
}
