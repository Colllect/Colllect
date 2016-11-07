<?php

namespace ApiBundle\Model;

class Image extends Element
{
    /**
     * {@inheritdoc}
     */
    public function shouldLoadContent()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this;
    }
}
