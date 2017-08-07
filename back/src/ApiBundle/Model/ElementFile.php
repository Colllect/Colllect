<?php

namespace ApiBundle\Model;

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


    public function __construct(Element $element = null)
    {
        $this->tags = [];

        if ($element !== null) {
            $this->setName($element->getName());
            $this->setTags($element->getTags());
            $this->setExtension($element->getExtension());

            if ($element->shouldLoadContent()) {
                $this->setContent($element->getContent());
            }
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
        if (!$this->name || !$this->extension) {
            return null;
        }

        $concatTags = implode('', array_map(function ($tag) {
            return ' #' . str_replace(' ', '_', $tag);
        }, $this->tags));

        return $this->name . $concatTags . '.' . $this->extension;
    }

    /**
     * @param string $basename
     * @return $this
     */
    public function setBasename(string $basename)
    {
        $basename = preg_replace('/[:;\[\]\/\?]+/i', '', $basename);

        $elementMeta = Element::parseBasename($basename);

        $this->setName($elementMeta['name']);
        $this->setTags($elementMeta['tags']);
        $this->setExtension($elementMeta['extension']);

        return $this;
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
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
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
     */
    public function setExtension(string $extension)
    {
        $this->extension = $extension;
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
     * @return $this
     * @throws \Exception
     */
    public function setType(string $type)
    {
        if (!in_array($type, array_keys(Element::EXTENSIONS_BY_TYPE))) {
            throw new \Exception('error.invalid_type');
        }

        $this->type = $type;

        return $this;
    }
}
