<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * ramsey/devtools-lib is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer\Command;

use Composer\Command\BaseCommand as ComposerBaseCommand;
use Composer\Console\Application;
use Composer\EventDispatcher\EventDispatcher;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function sprintf;
use function substr;

use const DIRECTORY_SEPARATOR;

/**
 * @psalm-consistent-constructor
 */
abstract class BaseCommand extends ComposerBaseCommand
{
    private Configuration $configuration;
    private string $binDir;
    private EventDispatcher $eventDispatcher;
    private bool $overrideDefault;

    /**
     * Returns the name of this command, without the command prefix
     */
    abstract public function getBaseName(): string;

    /**
     * Called by the execute() command in this BaseCommand class
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output): int;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $binDir = $configuration->getComposer()->getConfig()->get('bin-dir');
        assert(is_string($binDir));
        $this->binDir = $binDir;

        $this->eventDispatcher = $configuration->getComposer()->getEventDispatcher();
        $this->setComposer($configuration->getComposer());

        parent::__construct($this->withPrefix($this->getBaseName()));

        $extra = $configuration->getComposer()->getPackage()->getExtra();

        /** @var array{command-prefix?: string, commands?: array<string, mixed>} $devtoolsConfig */
        $devtoolsConfig = $extra['ramsey/devtools'] ?? [];

        /** @var array{override?: bool, script?: array<string>|string} $commandConfig */
        $commandConfig = $devtoolsConfig['commands'][$this->getBaseName()] ?? [];

        $this->overrideDefault = $commandConfig['override'] ?? false;

        $additionalScripts = (array) ($commandConfig['script'] ?? []);

        /** @var callable $script */
        foreach ($additionalScripts as $script) {
            $this->eventDispatcher->addListener((string) $this->getName(), $script);
        }
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        if (!$this->overrideDefault) {
            $exitCode = $this->doExecute($input, $output);
        }

        return $exitCode + $this->eventDispatcher->dispatchScript((string) $this->getName());
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getBinDir(): string
    {
        return $this->binDir;
    }

    public function withBinPath(string $bin): string
    {
        return $this->getBinDir() . DIRECTORY_SEPARATOR . $bin;
    }

    public function getPrefix(): string
    {
        $prefix = $this->configuration->getCommandPrefix();

        if ($prefix !== '' && substr($prefix, -1) !== ':') {
            $prefix .= ':';
        }

        return $prefix;
    }

    public function withPrefix(string $name): string
    {
        return $this->getPrefix() . $name;
    }

    public function getApplication(): Application
    {
        try {
            $application = parent::getApplication();
        } catch (RuntimeException $_e) {
            $application = null;
        }

        if (!$application instanceof Application) {
            throw new RuntimeException(sprintf(
                'Composer commands can only work with an %s instance set',
                Application::class,
            ));
        }

        return $application;
    }
}
