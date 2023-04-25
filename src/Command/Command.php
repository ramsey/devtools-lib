<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Composer\EventDispatcher\EventDispatcher;
use Ramsey\Dev\Tools\Composer\ExtraConfiguration;
use Ramsey\Dev\Tools\Configuration;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_column;
use function assert;
use function basename;
use function filter_var;
use function implode;
use function ini_set;
use function mb_strlen;
use function preg_replace;
use function preg_split;
use function str_replace;
use function trim;

use const FILTER_VALIDATE_BOOL;
use const PHP_EOL;
use const PREG_SPLIT_NO_EMPTY;

abstract class Command extends SymfonyCommand
{
    private const WRAP_WIDTH = 78;

    private const CUSTOM_HELP_TAGS = [
        [
            'pattern' => '/<code>(.*)<\/(?:code)?>/Us',
            'replacement' => '<fg=bright-blue>$1</>',
        ],
        [
            'pattern' => '/<file>(.*)<\/(?:file)?>/Us',
            'replacement' => '<fg=bright-magenta>$1</>',
        ],
        [
            'pattern' => '/<link>(.*)<\/(?:link)?>/Us',
            'replacement' => '<fg=cyan;options=underscore>$1</>',
        ],
    ];

    private ?EventDispatcher $eventDispatcher = null;
    private readonly ExtraConfiguration $extra;

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

        $this->extra = $this->buildComposerExtraConfiguration();
    }

    public function getExtra(): ExtraConfiguration
    {
        return $this->extra;
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->getExtra()->memoryLimit !== null) {
            ini_set('memory_limit', $this->getExtra()->memoryLimit);
        }

        // We must configure Composer's event dispatcher at execution time
        // because we need to create Composer's IO object from the command
        // execution input and output.
        $this->setupComposerEventDispatcher($input, $output);

        assert($this->eventDispatcher !== null);

        $exitCode = 0;

        if (!$this->getExtra()->override) {
            $exitCode = $this->doExecute($input, $output);
        }

        return $exitCode + $this->eventDispatcher->dispatchScript((string) $this->getName());
    }

    public function getHelp(): string
    {
        return $this->wrapHelp(
            $this->replaceHelpTokens(
                parent::getHelp(),
                (string) $this->getName(),
            ),
        );
    }

    public function getHelpForComposer(): string
    {
        return $this->wrapHelp(
            $this->replaceHelpTokens(
                parent::getHelp(),
                $this->getExtra()->getPrefixedCommandName(),
            ),
        );
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
    private function setupComposerEventDispatcher(InputInterface $input, OutputInterface $output): void
    {
        // We create a new Composer instance to configure it with this
        // execution's input and output.
        $composer = $this->configuration->composerFactory->getComposer($input, $output, $this->getHelperSet());

        $this->eventDispatcher = $composer->getEventDispatcher();
        $this->eventDispatcher->setRunScripts(true);

        foreach ($this->getExtra()->scripts as $script) {
            $this->eventDispatcher->addListener((string) $this->getName(), $script);
        }
    }

    private function buildComposerExtraConfiguration(): ExtraConfiguration
    {
        $property = $this->configuration->composerExtraProperty;

        /** @var array{command-prefix?: string, devtools?: mixed[]} $extra */
        $extra = $this->configuration->composer->getPackage()->getExtra();

        /** @var array{command-prefix?: string, commands?: array<string, array{override?: bool, script?: string | string[], memory-limit?: int | string}>, memory-limit?: int | string} $config */
        $config = $extra[$property] ?? $extra['devtools'] ?? [];

        /** @var array{override?: bool, script?: string | string[], memory-limit?: int | string} $commandConfig */
        $commandConfig = $config['commands'][(string) $this->getName()] ?? [];

        $commandPrefix = $config['command-prefix']
            ?? $extra['command-prefix']
            ?? ExtraConfiguration::DEFAULT_COMMAND_PREFIX;

        return new ExtraConfiguration(
            commandName: (string) $this->getName(),
            commandPrefix: $commandPrefix,
            scripts: (array) ($commandConfig['script'] ?? []),
            override: (bool) filter_var($commandConfig['override'] ?? false, (int) FILTER_VALIDATE_BOOL),
            memoryLimit: $commandConfig['memory-limit'] ?? $config['memory-limit'] ?? null,
        );
    }

    private function replaceHelpTokens(string $helpText, string $commandName): string
    {
        // The Symfony method Command::getParsedHelp() already does this, but
        // we do it here to account for proper line wrapping in wrapHelp().
        $placeholders = ['%command.name%', '%command.full_name%'];

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable
        $replacements = [$commandName, trim(basename(($_SERVER['PHP_SELF'] ?? '')) . ' ' . $commandName)];

        $helpText = str_replace($placeholders, $replacements, $helpText ?: $this->getDescription());

        // We could use OutputFormatterStyle for this, but when running in the
        // context of a Composer plugin, we're not able to apply those styles
        // to the Composer console application, so we must use replacements
        // instead.
        return (string) preg_replace(
            array_column(self::CUSTOM_HELP_TAGS, 'pattern'),
            array_column(self::CUSTOM_HELP_TAGS, 'replacement'),
            $helpText,
        );
    }
}
