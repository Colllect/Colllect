<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

final class EmptyFileException extends Exception
{
    public function __construct(string $message = 'error.empty_file')
    {
        parent::__construct($message);
    }
}
