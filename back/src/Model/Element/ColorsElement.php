<?php

declare(strict_types=1);

namespace App\Model\Element;

class ColorsElement extends AbstractElement
{
    private const TYPE_NAME = 'colors';

    /**
     * {@inheritdoc}
     */
    public static function getType(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedExtensions(): array
    {
        return [
            'colors',
        ];
    }

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
    public function setContent(string $content): void
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
