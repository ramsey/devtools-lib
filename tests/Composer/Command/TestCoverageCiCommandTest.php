<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\Tools\Composer\Command;

use Ramsey\Dev\Tools\Composer\Command\TestCoverageCiCommand;

class TestCoverageCiCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = TestCoverageCiCommand::class;
        $this->baseName = 'test:coverage:ci';
        $this->processCommand = [
            '/path/to/bin-dir/phpunit',
            '--colors=always',
            '--coverage-text',
            '--coverage-clover',
            'build/coverage/clover.xml',
            '--coverage-cobertura',
            'build/coverage/cobertura.xml',
            '--coverage-crap4j',
            'build/coverage/crap4j.xml',
            '--coverage-xml',
            'build/coverage/coverage-xml',
            '--log-junit',
            'build/junit.xml',
            '--group',
            'foo',
        ];

        parent::setUp();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--group', 'foo'],
        ]);
    }
}
