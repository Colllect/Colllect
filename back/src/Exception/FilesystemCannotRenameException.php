<?php

declare(strict_types=1);

namespace App\Exception;

final class FilesystemCannotRenameException extends \Exception
{
    public function __construct($message = 'error.filesystem_cannot_rename')
    {
        parent::__construct($message);
    }
}
