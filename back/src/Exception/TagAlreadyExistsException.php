<?php

declare(strict_types=1);

namespace App\Exception;

final class TagAlreadyExistsException extends \Exception
{
    public function __construct($message = 'error.tag_already_exists')
    {
        parent::__construct($message);
    }
}
