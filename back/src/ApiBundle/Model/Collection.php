<?php

namespace ApiBundle\Model;

use ApiBundle\Util\Base64;
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
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $encodedCollectionPath;


    /**
     * Collection constructor.
     * @param array $collectionMetadata
     */
    public function __construct(array $collectionMetadata = [])
    {
        if (count($collectionMetadata) > 0) {
            $this->setName($collectionMetadata['filename']);
            $this->setEncodedCollectionPath(Base64::encode($collectionMetadata['path']));
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

    /**
     * @return string
     */
    public function getEncodedCollectionPath(): string
    {
        return $this->encodedCollectionPath;
    }

    /**
     * @param string $encodedCollectionPath
     */
    public function setEncodedCollectionPath(string $encodedCollectionPath)
    {
        $this->encodedCollectionPath = $encodedCollectionPath;
    }
}
