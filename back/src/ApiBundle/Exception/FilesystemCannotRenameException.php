<?php

namespace ApiBundle\Exception;

final class FilesystemCannotRenameException extends \Exception
{
    public function __construct($message = 'error.filesystem_cannot_rename')
    {
        parent::__construct($message);
    }
}
