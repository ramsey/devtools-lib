<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools;

use PHPUnit\Framework\Attributes\TestWith;
use Ramsey\Dev\Tools\DevToolsApplication;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DevToolsApplicationTest extends TestCase
{
    #[TestWith(['help changelog', 'To get started with keep-a-changelog'])]
    #[TestWith(['help license', 'To get started with license-checker'])]
    #[TestWith(['help analyze:all', 'analyze:all is a shortcut'])]
    #[TestWith(['help analyze:phpstan', 'analyze:phpstan command executes PHPStan'])]
    #[TestWith(['help analyze:psalm', 'analyze:psalm command executes Psalm'])]
    #[TestWith(['help build:clean', 'You may use the build/ directory to store any artifacts'])]
    #[TestWith(['help build:clean:cache', 'This command erases only the contents of build/cache/'])]
    #[TestWith(['help build:clean:coverage', 'This command erases only the contents of build/coverage/'])]
    #[TestWith(['help lint:all', 'lint:all is a shortcut'])]
    #[TestWith(['help lint:fix', 'lint:fix command executes phpcbf'])]
    #[TestWith(['help lint:structure', 'lint:structure command executes pds-skeleton validate'])]
    #[TestWith(['help lint:style', 'lint:style command executes phpcs'])]
    #[TestWith(['help lint:syntax', 'parallel-lint is similar to php -l'])]
    #[TestWith(['help test:all', 'test:all is a shortcut'])]
    #[TestWith(['help test:coverage:ci', 'Coverage reports include'])]
    #[TestWith(['help test:coverage:html', 'HTML coverage report is saved'])]
    #[TestWith(['help test:unit', 'test:unit command executes phpunit'])]
    public function testRun(string $command, string $outputTest): void
    {
        $input = new StringInput($command . ' --no-ansi');
        $output = new BufferedOutput();

        $application = new DevToolsApplication();
        $application->setAutoExit(false);

        $this->assertSame(Command::SUCCESS, $application->run($input, $output));
        $this->assertStringContainsString($outputTest, $output->fetch());
    }
}
