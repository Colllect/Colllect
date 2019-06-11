<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

final class FilesystemCannotWriteException extends Exception
{
    public function __construct(string $message = 'error.filesystem_cannot_write')
    {
        parent::__construct($message);
    }
}
