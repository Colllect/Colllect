<?php

namespace ApiBundle\Model;

class Color extends Element
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

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return null;
    }
}
