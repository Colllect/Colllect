<?php

namespace AppBundle\Model;

use AppBundle\Util\Base64;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

trait FileTrait
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

        if ($this->type == null) {
            try {
                $this->type = AbstractElement::getElementType($this->basename);
            } catch (\Exception $e) {
            }
        }

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
        if (!in_array($type, array_keys(AbstractElement::EXTENSIONS_BY_TYPE))) {
            throw new \Exception('error.invalid_type');
        }

        $this->type = $type;

        return $this;
    }

    public function fetchContent()
    {
        if ($this->getFile() != null) {
            if ($this->getBasename() == null) {
                $this->setBasename($this->getFile()->getClientOriginalName());
            }
            $this->setContent(file_get_contents($this->getFile()->getRealPath()));
        } else if ($this->getUrl() != null) {
            if ($this->getBasename() == null) {
                $parsedUrl = parse_url($this->getUrl());
                $path = explode('/', $parsedUrl['path']);
                $this->setBasename(end($path));
            } else if ($this->getType() == null) {
                try {
                    $this->setType(AbstractElement::getElementType($this->getUrl()));
                } catch (\Exception $e) {
                }
            }
            if ($this->getType() == null) {
                $headers = get_headers($this->getUrl());
                if (strlen(strstr($headers[0], 'OK')) > 0) {
                    $this->setType(AbstractElement::LINK_TYPE);
                }
            }

            if ($this->getType() === AbstractElement::LINK_TYPE) {
                $this->setContent($this->getUrl());
                $pathInfos = pathinfo($this->getBasename());
                if (!isset($pathInfos['extension'])) {
                    $this->setBasename($this->getBasename() . '.link');
                }
            } else if (in_array($this->getType(), [AbstractElement::IMAGE_TYPE, AbstractElement::NOTE_TYPE])) {
                $this->setContent(file_get_contents($this->getUrl()));

                if ($this->getType() == AbstractElement::NOTE_TYPE) {
                    $pathInfos = pathinfo($this->getBasename());
                    if (!isset($pathInfos['extension'])) {
                        $this->setBasename($this->getBasename() . '.md');
                    }
                }
            }
        } else if ($this->getContent() != null) {
            if (!Base64::isValidBase64($this->getContent())) {
                $this->setContent(null);
                throw new \Exception('error.non_json_content');
            }
        }
    }
}
