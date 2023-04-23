<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class DevToolsApplication extends Application
{
    public const VERSION = '2.x-dev';

    public function __construct(public readonly Configuration $configuration = new Configuration())
    {
        parent::__construct('ramsey/devtools', self::VERSION);

        $this->registerCommands();
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        $output ??= new ConsoleOutput();

        $codeStyle = new OutputFormatterStyle('bright-blue');
        $output->getFormatter()->setStyle('code', $codeStyle);

        $fileStyle = new OutputFormatterStyle('bright-magenta');
        $output->getFormatter()->setStyle('file', $fileStyle);

        $linkStyle = new OutputFormatterStyle('cyan', options: ['underscore']);
        $output->getFormatter()->setStyle('link', $linkStyle);

        return parent::run($input, $output);
    }

    private function registerCommands(): void
    {
        $this->addCommands([
            new Command\Analyze\AllCommand($this->configuration),
            new Command\Analyze\PhpStanCommand($this->configuration),
            new Command\Analyze\PsalmCommand($this->configuration),
            new Command\Build\CleanCommand($this->configuration),
            new Command\Build\Clean\CacheCommand($this->configuration),
            new Command\Build\Clean\CoverageCommand($this->configuration),
            new Command\ChangelogCommand($this->configuration),
            new Command\LicenseCommand($this->configuration),
            new Command\Lint\AllCommand($this->configuration),
            new Command\Lint\FixCommand($this->configuration),
            new Command\Lint\StructureCommand($this->configuration),
            new Command\Lint\StyleCommand($this->configuration),
            new Command\Lint\SyntaxCommand($this->configuration),
            new Command\Test\AllCommand($this->configuration),
            new Command\Test\Coverage\CiCommand($this->configuration),
            new Command\Test\Coverage\HtmlCommand($this->configuration),
            new Command\Test\UnitCommand($this->configuration),
        ]);
    }
}
