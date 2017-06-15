<?php

namespace ApiBundle\Model;

use JMS\Serializer\Annotation as Serializer;

class Token
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $token;


    function __construct(string $token)
    {
        $this->token = $token;
    }
}
