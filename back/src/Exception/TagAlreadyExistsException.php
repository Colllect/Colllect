<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

final class TagAlreadyExistsException extends Exception
{
    public function __construct(string $message = 'error.tag_already_exists')
    {
        parent::__construct($message);
    }
}
