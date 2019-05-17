<?php

declare(strict_types=1);

namespace App\Model\Element;

use App\Exception\NotSupportedElementTypeException;
use App\Util\Base64;
use App\Util\ElementBasenameParser;
use DateTime;
use Swagger\Annotations as SWG;

abstract class AbstractElement implements ElementInterface
{
    /**
     * @var string
     *
     * @SWG\Property(type="string")
     */
    private $name;

    /**
     * @var array
     *
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(type="string")
     * )
     */
    private $tags;

    /**
     * @var DateTime
     *
     * @SWG\Property(type="string", format="date-time")
     */
    private $updated;

    /**
     * @var int
     *
     * @SWG\Property(type="integer")
     */
    private $size;

    /**
     * @var string
     *
     * @SWG\Property(type="string")
     */
    private $extension;

    /**
     * @var string
     *
     * @SWG\Property(type="string")
     */
    private $encodedColllectionPath;

    /**
     * @var string
     *
     * @SWG\Property(type="string")
     */
    private $encodedElementBasename;

    /**
     * @var string
     *
     * @SWG\Property(type="string")
     */
    private $fileUrl;

    /**
     * Element constructor.
     *
     * @param string[] $meta
     *
     * @throws NotSupportedElementTypeException
     */
    public function __construct(array $meta, string $encodedColllectionPath)
    {
        $basename = pathinfo($meta['path'])['basename'];
        $elementMeta = ElementBasenameParser::parse($basename);

        $updated = new DateTime();
        $updated->setTimestamp($meta['timestamp']);

        $this->name = $elementMeta['name'];
        $this->tags = $elementMeta['tags'];
        $this->updated = $updated;
        $this->size = $meta['size'];
        $this->extension = $elementMeta['extension'];
        $this->encodedColllectionPath = $encodedColllectionPath;
        $this->encodedElementBasename = Base64::encode($basename);
    }

    /**
     * @SWG\Property(type="string")
     */
    public function getType(): string
    {
        return $this::getElementType();
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
     * @return ElementInterface
     *
     * @throws NotSupportedElementTypeException
     */
    public static function get(array $elementMetadata, string $encodedColllectionPath): ElementInterface
    {
        $type = ElementBasenameParser::getTypeByPath($elementMetadata['path']);
        switch ($type) {
            case ImageElement::getElementType():
                return new ImageElement($elementMetadata, $encodedColllectionPath);
                break;
        }

        throw new NotSupportedElementTypeException();
    }
}
