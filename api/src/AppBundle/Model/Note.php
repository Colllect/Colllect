<?php

namespace AppBundle\Model;

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
    public function shouldLoadContent()
    {
        return true;
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
}
