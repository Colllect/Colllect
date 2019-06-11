<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

final class NotSupportedElementTypeException extends Exception
{
    public function __construct(string $message = 'error.unsupported_element_type')
    {
        parent::__construct($message);
    }
}
