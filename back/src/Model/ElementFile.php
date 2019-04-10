<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\NotSupportedElementTypeException;
use App\Util\Base64;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ElementFile
{
    /**
     * @var UploadedFile
     * @Assert\File()
     */
    protected $file;

    /**
     * @var string
     * @Assert\Url()
     */
    protected $url;

    /**
     * @var string
     * @Assert\Type("string")
     */
    protected $content;

    /**
     * @var string
     * @Assert\Type("string")
     */
    protected $name;

    /**
     * @var array
     * @Assert\Type("array")
     */
    protected $tags;

    /**
     * @var string
     * @Assert\Type("string")
     */
    protected $extension;

    /**
     * @var string
     * @Assert\Type("string")
     */
    protected $type;

    /**
     * @var string
     */
    private $basename;

    public function __construct(Element $element = null)
    {
        $this->tags = [];

        if ($element !== null) {
            $this->name = $element->getName();
            $this->tags = $element->getTags();
            $this->extension = $element->getExtension();
            $this->basename = Base64::decode($element->getEncodedElementBasename());
        }
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return ElementFile $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return ElementFile $this
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return ElementFile $this
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * @param string $basename
     *
     * @return ElementFile $this
     *
     * @throws NotSupportedElementTypeException
     */
    public function setBasename(string $basename): self
    {
        $this->basename = $basename;

        // Remove illegal chars
        $basename = preg_replace('/[:;\[\]\/\?]+/i', '', $basename);

        $elementMeta = Element::parseBasename($basename);

        $this->name = $elementMeta['name'];
        $this->tags = $elementMeta['tags'];
        $this->extension = $elementMeta['extension'];

        return $this;
    }

    /**
     * @return string
     */
    public function getCleanedBasename()
    {
        if (!$this->name || !$this->extension) {
            return null;
        }

        $concatTags = implode(
            '',
            array_map(
                function ($tag) {
                    return ' #' . str_replace(' ', '_', $tag);
                },
                $this->tags
            )
        );

        return $this->name . $concatTags . '.' . $this->extension;
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
     *
     * @return ElementFile $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     *
     * @return ElementFile $this
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Add a tag to element file.
     *
     * @param string $tag
     *
     * @return ElementFile $this
     */
    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove a tag from element file.
     *
     * @param string $tag
     *
     * @return ElementFile $this
     */
    public function removeTag(string $tag): self
    {
        $this->tags = array_filter(
            $this->tags,
            function (string $existingTagName) use ($tag) {
                return $tag !== $existingTagName;
            }
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return ElementFile $this
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ElementFile $this
     *
     * @throws Exception
     */
    public function setType(string $type)
    {
        if (!\in_array($type, array_keys(Element::EXTENSIONS_BY_TYPE), true)) {
            throw new Exception('error.invalid_type');
        }

        $this->type = $type;

        return $this;
    }
}
