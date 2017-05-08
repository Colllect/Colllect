<?php

namespace ApiBundle\Model;

use ApiBundle\Exception\NotSupportedElementTypeException;
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
     * @Serializer\Expose()
     */
    private $type;

    /**
     * @var string
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var array
     * @Serializer\Expose()
     */
    private $tags;

    /**
     * @var DateTime
     * @Serializer\Expose()
     */
    private $updated;

    /**
     * @var int
     * @Serializer\Expose()
     */
    private $size;

    /**
     * @var string
     * @Serializer\Expose()
     */
    private $extension;


    public function __construct(array $meta)
    {
        // Can throw an NotSupportedElementTypeException
        $this->type = self::getTypeByPath($meta['path']);

        $pathParts = pathinfo($meta['path']);
        $filename = $pathParts['filename'];

        // Parse tags from filename
        preg_match_all('/#([^\s.,\/#!$%\^&\*;:{}=\-`~()]+)/', $filename, $tags);
        $tags = $tags[1];
        foreach ($tags as $k => $tag) {
            // Remove tags from filename
            $filename = str_replace("#$tag", "", $filename);
            // Replace underscores by spaces in tags
            $tags[$k] = str_replace("_", " ", $tag);
        }
        // Replace multiple spaces by single space
        $name = preg_replace('/\s+/', ' ', trim($filename));

        $updated = new DateTime();
        $updated->setTimestamp($meta['timestamp']);

        $this->name = $name;
        $this->tags = $tags;
        $this->updated = $updated;
        $this->size = $meta['size'];
        $this->extension = $pathParts['extension'];
    }

    /**
     * Determinate if this type of element should have his content loaded in response object
     *
     * @return bool
     */
    abstract public function shouldLoadContent();

    /**
     * Set content of the file in the element object
     * It can be handled differently by each element typed class
     *
     * @param $content
     * @return $this
     */
    abstract public function setContent($content);

    /**
     * Get element type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get element name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get element tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get element last updated date
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get element size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get element extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param $elementFilePath
     * @return string
     * @throws \Exception
     */
    public static function getTypeByPath($elementFilePath)
    {
        $pathInfos = pathinfo($elementFilePath);
        if (isset($pathInfos['extension'])) {
            foreach (self::EXTENSIONS_BY_TYPE as $type => $extensions) {
                if (in_array($pathInfos['extension'], $extensions)) {
                    return $type;
                }
            }
        }

        throw new NotSupportedElementTypeException();
    }

    /**
     * Return typed element based on flysystem metadata
     *
     * @param array $elementMetadata
     * @return Color|Image|Link|Note
     * @throws NotSupportedElementTypeException
     */
    public static function get($elementMetadata)
    {
        $type = self::getTypeByPath($elementMetadata['path']);
        switch($type) {
            case self::COLORS_TYPE:
                return new Color($elementMetadata);
                break;
            case self::IMAGE_TYPE:
                return new Image($elementMetadata);
                break;
            case self::LINK_TYPE:
                return new Link($elementMetadata);
                break;
            case self::NOTE_TYPE:
                return new Note($elementMetadata);
                break;
        }

        throw new NotSupportedElementTypeException();
    }
}
