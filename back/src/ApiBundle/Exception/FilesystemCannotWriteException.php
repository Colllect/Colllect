<?php

namespace ApiBundle\Exception;

final class FilesystemCannotWriteException extends \Exception
{
    public function __construct($message = 'error.filesystem_cannot_write')
    {
        parent::__construct($message);
    }
}
