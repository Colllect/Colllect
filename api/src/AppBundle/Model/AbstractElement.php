<?php

namespace AppBundle\Model;

use AppBundle\Exception\NotSupportedElementTypeException;
use DateTime;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractElement
{
    use UpdatableElementTrait;

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
    protected $name;

    /**
     * @var array
     * @Serializer\Expose()
     */
    protected $tags;

    /**
     * @var DateTime
     * @Serializer\Expose()
     */
    protected $updated;

    /**
     * @var int
     * @Serializer\Expose()
     */
    protected $size;

    /**
     * @var string
     * @Serializer\Expose()
     */
    protected $extension;


    public function __construct(array $meta)
    {
        if (!self::isValidElement($meta['path'])) {
            throw new NotSupportedElementTypeException();
        }

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
     * @param string $path
     * @return bool
     */
    public static function isValidElement($path)
    {
        return self::getElementType($path) !== null;
    }

    /**
     * @param string $path
     * @return string|null
     */
    public static function getElementType($path)
    {
        $extension = pathinfo($path)['extension'];
        foreach (self::EXTENSIONS_BY_TYPE as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }

        return null;
    }
}
