<?php

declare(strict_types=1);

namespace App\Model;

use App\Util\Base64;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Colllection
{
    /**
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $encodedColllectionPath;

    public function __construct(array $colllectionMetadata = [])
    {
        if (\count($colllectionMetadata) > 0) {
            $this->setName($colllectionMetadata['filename']);
            $this->setEncodedColllectionPath(Base64::encode($colllectionMetadata['path']));
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEncodedColllectionPath(): ?string
    {
        return $this->encodedColllectionPath;
    }

    public function setEncodedColllectionPath(string $encodedColllectionPath): self
    {
        $this->encodedColllectionPath = $encodedColllectionPath;

        return $this;
    }
}
