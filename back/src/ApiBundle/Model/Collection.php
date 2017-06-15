<?php

namespace ApiBundle\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Collection
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $name;


    /**
     * Collection constructor.
     * @param array $collectionMetadata
     */
    function __construct(array $collectionMetadata = [])
    {
        if (count($collectionMetadata) > 0) {
            $this->setName($collectionMetadata['filename']);
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
    }
}
