<?php

namespace AppBundle\Model;

use AppBundle\Exception\NotSupportedElementTypeException;
use AppBundle\Util\Base64;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
