<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

final class DropboxAccessTokenMissingException extends Exception
{
    public function __construct(string $message = 'error.dropbox_not_linked')
    {
        parent::__construct($message);
    }
}
