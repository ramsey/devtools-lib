<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Command;

use Ramsey\Dev\Tools\DevToolsException;
use RuntimeException;

class ExecutableNotFound extends RuntimeException implements DevToolsException
{
}
