<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Composer\Composer;
use Composer\EventDispatcher\EventDispatcher;
use Ramsey\Dev\Tools\Configuration;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function implode;
use function mb_strlen;
use function preg_replace;
use function preg_split;
use function str_replace;
use function trim;

use const PHP_EOL;
use const PREG_SPLIT_NO_EMPTY;

abstract class Command extends SymfonyCommand
{
    private const WRAP_WIDTH = 78;

    private ?EventDispatcher $eventDispatcher = null;
    private bool $overrideDefault = false;

    /**
     * The execute() method in this class is set `final` to ensure it will
     * always handle dispatching scripts found in `composer.json` for commands
     * with the same name. So, children of this class must implement doExecute()
     * to perform any work.
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output): int;

    public function __construct(protected readonly Configuration $configuration)
    {
        parent::__construct(null);
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // We must configure Composer at execution time because we need to create
        // Composer's IO object from the command execution input and output.
        $this->configureWithComposer(
            $this->configuration->composerFactory->getComposer(
                $input,
                $output,
                $this->getHelperSet(),
            ),
        );

        assert($this->eventDispatcher !== null);

        $exitCode = 0;

        if (!$this->overrideDefault) {
            $exitCode = $this->doExecute($input, $output);
        }

        return $exitCode + $this->eventDispatcher->dispatchScript((string) $this->getName());
    }

    public function setHelp(string $help): static
    {
        $name = (string) $this->getName();

        // The Symfony method Command::getParsedHelp() already does this, but
        // we do it here to account for proper line wrapping in wrapHelp().
        $placeholders = ['%command.name%', '%command.full_name%'];

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable
        $replacements = [$name, trim(($_SERVER['PHP_SELF'] ?? '') . ' ' . $name)];

        $help = str_replace($placeholders, $replacements, $help ?: $this->getDescription());

        return parent::setHelp($this->wrapHelp($help));
    }

    private function wrapHelp(string $message): string
    {
        $message = (string) preg_replace('/(?<!\n|\r|\r\n)\R(?!\s)/', ' ', $message);
        $message = (string) preg_replace('/\R/', PHP_EOL, $message);
        $lines = preg_split('/\R/', $message) ?: [];

        $wrapped = [];

        foreach ($lines as $line) {
            if (mb_strlen($line) <= self::WRAP_WIDTH) {
                $wrapped[] = $line;

                continue;
            }

            $buffer = '';
            $tokens = preg_split('/[[:space:]]/', $line, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($tokens as $token) {
                if ($buffer === '') {
                    $buffer = $token;

                    continue;
                }

                $lengthCheckToken = (string) preg_replace('/<[^>]+>|<\/[^>]*>/', '', "$buffer $token");

                if (mb_strlen($lengthCheckToken) <= self::WRAP_WIDTH) {
                    $buffer .= " $token";

                    continue;
                }

                $wrapped[] = $buffer;
                $buffer = $token;
            }

            // If anything is left in the buffer, add it to the wrapped lines.
            if ($buffer !== '') {
                $wrapped[] = $buffer;
            }
        }

        return implode(PHP_EOL, $wrapped);
    }

    /**
     * Use extra.devtools, if available, but extra.ramsey/devtools
     * takes precedence over extra.devtools.
     */
    private function configureWithComposer(Composer $composer): void
    {
        $this->eventDispatcher = $composer->getEventDispatcher();
        $this->eventDispatcher->setRunScripts(true);

        $extra = $composer->getPackage()->getExtra();

        /** @var array{command-prefix?: string, commands?: array<string, mixed>} $devtoolsConfig */
        $devtoolsConfig = $extra[$this->configuration->composerExtraProperty] ?? $extra['devtools'] ?? [];

        /** @var array{override?: bool, script?: array<string> | string} $commandConfig */
        $commandConfig = $devtoolsConfig['commands'][(string) $this->getName()] ?? [];

        $this->overrideDefault = $commandConfig['override'] ?? false;

        $additionalScripts = (array) ($commandConfig['script'] ?? []);

        foreach ($additionalScripts as $script) {
            $this->eventDispatcher->addListener((string) $this->getName(), $script);
        }
    }
}
