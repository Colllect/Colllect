<?php

declare(strict_types=1);

namespace App\Model\Element;

use App\Exception\NotSupportedElementTypeException;
use App\Util\Base64;
use App\Util\ElementBasenameParser;
use DateTime;

abstract class AbstractElement implements ElementInterface
{
    /* @var string */
    private $name;

    /* @var array */
    private $tags;

    /* @var DateTime */
    private $updated;

    /* @var int */
    private $size;

    /* @var string */
    private $extension;

    /* @var string */
    private $encodedColllectionPath;

    /* @var string */
    private $encodedElementBasename;

    /* @var string */
    private $fileUrl;

    /**
     * Element constructor.
     *
     * @param mixed[] $meta
     *
     * @throws NotSupportedElementTypeException
     */
    public function __construct(array $meta, string $encodedColllectionPath)
    {
        $basename = pathinfo($meta['path'])['basename'];
        $elementMeta = ElementBasenameParser::parse($basename);

        $updated = new DateTime();
        $updated->setTimestamp((int) $meta['timestamp']);

        $this->name = $elementMeta['name'];
        $this->tags = $elementMeta['tags'];
        $this->updated = $updated;
        $this->size = $meta['size'];
        $this->extension = $elementMeta['extension'];
        $this->encodedColllectionPath = $encodedColllectionPath;
        $this->encodedElementBasename = Base64::encode($basename);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncodedColllectionPath(): string
    {
        return $this->encodedColllectionPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodedColllectionPath(string $encodedColllectionPath): void
    {
        $this->encodedColllectionPath = $encodedColllectionPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncodedElementBasename(): string
    {
        return $this->encodedElementBasename;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodedElementBasename(string $encodedElementBasename): void
    {
        $this->encodedElementBasename = $encodedElementBasename;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileUrl(string $fileUrl): void
    {
        $this->fileUrl = $fileUrl;
    }

    /**
     * Return typed element based on flysystem metadata.
     *
     * @param string[] $elementMetadata
     *
     * @throws NotSupportedElementTypeException
     */
    public static function get(array $elementMetadata, string $encodedColllectionPath): ElementInterface
    {
        $type = ElementBasenameParser::getTypeByPath($elementMetadata['path']);
        switch ($type) {
            case ImageElement::getType():
                return new ImageElement($elementMetadata, $encodedColllectionPath);
                break;
        }

        throw new NotSupportedElementTypeException();
    }
}
