<?php

declare(strict_types=1);

namespace App\Model\Element;

use Swagger\Annotations as SWG;

class NoteElement extends AbstractElement
{
    /**
     * @var string
     */
    private const TYPE_NAME = 'note';

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
            'txt',
            'md',
        ];
    }

    /**
     * @SWG\Property(type="string")
     */
    private string $content;

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
     * {@inheritdoc}
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
