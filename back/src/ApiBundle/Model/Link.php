<?php

namespace ApiBundle\Model;

use JMS\Serializer\Annotation as Serializer;

class Link extends Element
{
    /**
     * @var string
     * @Serializer\Expose()
     */
    private $url;


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
     * {@inheritdoc}
     */
    public function shouldLoadContent()
    {
        return true;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->url = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->url;
    }
}
