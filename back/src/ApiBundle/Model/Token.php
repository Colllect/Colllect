<?php

namespace ApiBundle\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * This class is only used to serialize token
 * in responses via JMS Serializer
 */
class Token
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $token;


    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }
}
