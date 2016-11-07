<?php

namespace ApiBundle\Exception;

final class NotSupportedElementTypeException extends \Exception
{
    public function __construct($message = 'error.unsupported_element_type')
    {
        parent::__construct($message);
    }
}
