<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function rtrim;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;

final class ComposerCommand extends BaseCommand
{
    public readonly Command $wrappedCommand;
    public readonly string $originalName;

    public function __construct(Command $wrappedCommand, string $commandPrefix)
    {
        $this->wrappedCommand = clone $wrappedCommand;
        $this->originalName = (string) $wrappedCommand->getName();

        $name = (string) $this->wrappedCommand->getName();
        if ($commandPrefix !== '' && $commandPrefix !== ':') {
            $name = rtrim($commandPrefix, ':') . ":$name";
        }

        $this->wrappedCommand->setName($name);
        parent::__construct($name);
    }

    protected function configure(): void
    {
        /** @var string[] $aliases */
        $aliases = $this->wrappedCommand->getAliases();

        // Replace any occurrences of "name" in the help message with "prefix:name."
        $help = str_replace($this->originalName, (string) $this->getName(), $this->wrappedCommand->getHelp());

        $this
            ->setAliases($aliases)
            ->setDescription($this->wrappedCommand->getDescription())
            ->setHelp($help)
            ->setDefinition($this->wrappedCommand->getDefinition())
            ->setHidden($this->wrappedCommand->isHidden());

        /** @var string $usage */
        foreach ($this->wrappedCommand->getUsages() as $usage) {
            if (str_starts_with($usage, (string) $this->wrappedCommand->getName())) {
                // Trim off the leading command name and space before adding the usage.
                $usage = substr($usage, strlen((string) $this->wrappedCommand->getName()) + 1);
            }
            $this->addUsage($usage);
        }
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->wrappedCommand->run($input, $output);
    }
}