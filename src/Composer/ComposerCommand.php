<?php

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer;

use Composer\Command\BaseCommand;
use Ramsey\Dev\Tools\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function str_starts_with;
use function strlen;
use function substr;

final class ComposerCommand extends BaseCommand
{
    public readonly Command $wrappedCommand;

    public function __construct(Command $wrappedCommand)
    {
        $this->wrappedCommand = $wrappedCommand;

        parent::__construct($this->wrappedCommand->getExtra()->getPrefixedCommandName());
    }

    public function getHelp(): string
    {
        return $this->wrappedCommand->getHelpForComposer();
    }

    protected function configure(): void
    {
        /** @var string[] $aliases */
        $aliases = $this->wrappedCommand->getAliases();

        $this
            ->setAliases($aliases)
            ->setDescription($this->wrappedCommand->getDescription())
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
