<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\NotSupportedElementTypeException;
use App\Util\Base64;
use DateTime;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
abstract class Element
{
    const IMAGE_TYPE = 'image';
    const NOTE_TYPE = 'note';
    const LINK_TYPE = 'link';
    const COLORS_TYPE = 'colors';

    const EXTENSIONS_BY_TYPE = [
        self::IMAGE_TYPE => ['jpg', 'jpeg', 'png', 'gif'],
        self::NOTE_TYPE => ['txt', 'md'],
        self::LINK_TYPE => ['link'],
        self::COLORS_TYPE => ['colors'],
    ];

    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $type;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var array
     *
     * @Serializer\Type("array<string>")
     * @Serializer\Expose()
     */
    private $tags;

    /**
     * @var DateTime
     *
     * @Serializer\Type("DateTime")
     * @Serializer\Expose()
     */
    private $updated;

    /**
     * @var int
     *
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $size;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $extension;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $encodedColllectionPath;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $encodedElementBasename;

    /**
     * @var string
     *
     * @Serializer\Accessor(getter="getProxyUrl")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $proxyUrl;

    public function __construct(array $meta, string $encodedColllectionPath)
    {
        $basename = pathinfo($meta['path'])['basename'];
        $elementMeta = self::parseBasename($basename);

        $updated = new DateTime();
        $updated->setTimestamp($meta['timestamp']);

        $this->type = $elementMeta['type'];
        $this->name = $elementMeta['name'];
        $this->tags = $elementMeta['tags'];
        $this->updated = $updated;
        $this->size = $meta['size'];
        $this->extension = $elementMeta['extension'];
        $this->encodedColllectionPath = $encodedColllectionPath;
        $this->encodedElementBasename = Base64::encode($basename);
    }

    /**
     * Determinate if this type of element should have his content loaded in response object.
     *
     * @return bool
     */
    abstract public static function shouldLoadContent(): bool;

    /**
     * Get content of the file from the element object
     * It can be handled differently by each element typed class.
     *
     * @return string|null
     */
    abstract public function getContent(): ?string;

    /**
     * Set content of the file in the element object
     * It can be handled differently by each element typed class.
     *
     * @param $content
     *
     * @return $this
     */
    abstract public function setContent($content): self;

    /**
     * Get element type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get element name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set element name.
     *
     * @param string $name
     *
     * @return Element
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get element tags.
     *
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Get element last updated date.
     *
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * Get element size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get element extension.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Get element's Colllection encoded path.
     *
     * @return string
     */
    public function getEncodedColllectionPath(): string
    {
        return $this->encodedColllectionPath;
    }

    /**
     * Set element's Colllection encoded path.
     *
     * @param string $encodedColllectionPath
     */
    public function setEncodedColllectionPath(string $encodedColllectionPath): void
    {
        $this->encodedColllectionPath = $encodedColllectionPath;
    }

    public function getEncodedElementBasename(): string
    {
        return $this->encodedElementBasename;
    }

    public function setEncodedElementBasename(string $encodedElementBasename): self
    {
        $this->encodedElementBasename = $encodedElementBasename;

        return $this;
    }

    public function getProxyUrl(): string
    {
        return '/proxy/' . $this->encodedColllectionPath . '/' . $this->encodedElementBasename;
    }

    /**
     * @param string $elementFilePath
     *
     * @return string
     *
     * @throws NotSupportedElementTypeException
     */
    public static function getTypeByPath(string $elementFilePath): string
    {
        $pathInfos = pathinfo($elementFilePath);
        if (isset($pathInfos['extension'])) {
            foreach (self::EXTENSIONS_BY_TYPE as $type => $extensions) {
                if (\in_array($pathInfos['extension'], $extensions, true)) {
                    return $type;
                }
            }
        }

        throw new NotSupportedElementTypeException();
    }

    /**
     * Return typed element based on flysystem metadata.
     *
     * @param array  $elementMetadata
     * @param string $encodedColllectionPath
     *
     * @return Color|Image|Link|Note
     *
     * @throws NotSupportedElementTypeException
     */
    public static function get(array $elementMetadata, string $encodedColllectionPath): self
    {
        $type = self::getTypeByPath($elementMetadata['path']);
        switch ($type) {
            case self::IMAGE_TYPE:
                return new Image($elementMetadata, $encodedColllectionPath);
                break;
        }

        throw new NotSupportedElementTypeException();
    }

    /**
     * Parse basename to get type, name, tags and extension.
     *
     * @param string $basename
     *
     * @return array
     *
     * @throws NotSupportedElementTypeException
     */
    public static function parseBasename(string $basename): array
    {
        $meta = [];

        // Can throw an NotSupportedElementTypeException
        $meta['type'] = self::getTypeByPath($basename);

        $pathParts = pathinfo($basename);
        $filename = $pathParts['filename'];

        // Parse tags from filename
        preg_match_all('/#([^\s.,\/#!$%\^&\*;:{}=\-`~()]+)/', $filename, $tags);
        $tags = $tags[1];
        foreach ($tags as $k => $tag) {
            // Remove tags from filename
            $filename = str_replace("#$tag", '', $filename);
            // Replace underscores by spaces in tags
            $tags[$k] = str_replace('_', ' ', $tag);
        }
        sort($tags);

        // Replace multiple spaces by single space
        $name = preg_replace('/\s+/', ' ', trim($filename));

        $meta['name'] = $name;
        $meta['tags'] = $tags;
        $meta['extension'] = $pathParts['extension'];

        return $meta;
    }
}
