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

final class DevToolsApplication extends Application
{
    public const VERSION = '2.1.1';

    public function __construct(public readonly Configuration $configuration = new Configuration())
    {
        parent::__construct('ramsey/devtools', self::VERSION);

        $this->registerCommands();
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
