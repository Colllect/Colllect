<?php

declare(strict_types=1);

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;

class Note extends Element
{
    /**
     * @var string
     * @Serializer\Expose()
     */
    private $content;

    /**
     * {@inheritdoc}
     */
    public static function shouldLoadContent(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content): Element
    {
        $this->content = $content;

        return $this;
    }
}
