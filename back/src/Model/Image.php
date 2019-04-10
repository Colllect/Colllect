<?php

declare(strict_types=1);

namespace App\Model;

class Image extends Element
{
    /**
     * {@inheritdoc}
     */
    public static function shouldLoadContent(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content): void
    {
        // Nothing to do here since shouldLoadContent returns false
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): ?string
    {
        // Nothing to return here since shouldLoadContent returns false
        return null;
    }
}
