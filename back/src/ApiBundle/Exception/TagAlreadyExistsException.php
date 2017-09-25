<?php

namespace ApiBundle\Exception;

final class TagAlreadyExistsException extends \Exception
{
    public function __construct($message = 'error.tag_already_exists')
    {
        parent::__construct($message);
    }
}
