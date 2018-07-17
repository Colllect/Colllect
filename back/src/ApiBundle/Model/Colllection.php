<?php

namespace ApiBundle\Model;

use ApiBundle\Util\Base64;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Colllection
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
    private $encodedColllectionPath;


    /**
     * Colllection constructor.
     * @param array $colllectionMetadata
     */
    public function __construct(array $colllectionMetadata = [])
    {
        if (count($colllectionMetadata) > 0) {
            $this->setName($colllectionMetadata['filename']);
            $this->setEncodedColllectionPath(Base64::encode($colllectionMetadata['path']));
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
    public function getEncodedColllectionPath(): string
    {
        return $this->encodedColllectionPath;
    }

    /**
     * @param string $encodedColllectionPath
     */
    public function setEncodedColllectionPath(string $encodedColllectionPath)
    {
        $this->encodedColllectionPath = $encodedColllectionPath;
    }
}
