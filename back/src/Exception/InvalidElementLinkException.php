<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

final class InvalidElementLinkException extends Exception
{
    public function __construct(string $message = 'error.invalid_link')
    {
        parent::__construct($message);
    }
}
