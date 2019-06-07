<?php

declare(strict_types=1);

namespace App\Model\Element;

use Swagger\Annotations as SWG;

class LinkElement extends AbstractElement
{
    private const TYPE_NAME = 'link';

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
            'link',
        ];
    }

    /**
     * @var string
     *
     * @SWG\Property(type="string")
     */
    private $url;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
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
     * {@inheritdoc}
     */
    public function setContent(string $content): void
    {
        $this->url = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->url;
    }
}
