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

namespace Ramsey\Dev\Tools\Filesystem;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

use function dirname;
use function file_put_contents;
use function method_exists;

use const FILE_APPEND;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Filesystem extends SymfonyFilesystem
{
    /**
     * @inheritDoc
     */
    public function appendToFile(string $filename, $content): void
    {
        if (method_exists(SymfonyFilesystem::class, 'appendToFile')) {
            parent::appendToFile($filename, $content);

            return;
        }

        $this->mkdir(dirname($filename));
        file_put_contents($filename, $content, FILE_APPEND);
    }
}
