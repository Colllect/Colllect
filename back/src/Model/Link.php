<?php

declare(strict_types=1);

namespace App\Model;

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
     *
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
    public static function shouldLoadContent(): bool
    {
        return true;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content): Element
    {
        $this->url = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->url;
    }
}
