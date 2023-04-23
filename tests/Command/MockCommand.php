<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MockCommand extends Command
{
    public int $exitCode = 0;
    public int $calledCount = 0;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->calledCount++;

        return $this->exitCode;
    }
}
