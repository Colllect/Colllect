<?php

namespace ApiBundle\Model;

use ApiBundle\Util\Base64;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Tag
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $encodedName;


    /**
     * Tag constructor.
     * @param array $flatTag Tag as flat array
     */
    public function __construct(array $flatTag = [])
    {
        if (array_key_exists('name', $flatTag)) {
            $this->setName($flatTag['name']);
        }
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
        $this->encodedName = Base64::encode($name);
    }
}
