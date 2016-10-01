<?php

namespace AppBundle\Exception;

final class DropboxAccessTokenMissingException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}