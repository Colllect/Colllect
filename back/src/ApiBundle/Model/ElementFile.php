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
     */
    protected $basename;

    /**
     * @var string
     */
    protected $type;


    /**
     * @return mixed
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
    public function setUrl($url)
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
    public function setContent($content)
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
     * @return $this
     */
    public function setBasename($basename)
    {
        $this->basename = $basename;

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
     *
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(Element::EXTENSIONS_BY_TYPE))) {
            throw new \Exception('error.invalid_type');
        }

        $this->type = $type;

        return $this;
    }
}
