<?php

declare(strict_types=1);

namespace App\Exception;

final class DropboxAccessTokenMissingException extends \Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
