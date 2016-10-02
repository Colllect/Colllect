<?php

namespace AppBundle\Exception;

final class NotSupportedElementTypeException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
