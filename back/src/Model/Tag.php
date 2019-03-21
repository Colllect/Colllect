<?php

declare(strict_types=1);

namespace App\Model;

use App\Util\Base64;
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
     *
     * @param array $flatTag Tag as flat array
     */
    public function __construct(array $flatTag = [])
    {
        if (\array_key_exists('name', $flatTag)) {
            $this->setName($flatTag['name']);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Tag
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        $this->encodedName = Base64::encode($name);

        return $this;
    }
}
