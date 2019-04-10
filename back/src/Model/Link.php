<?php

declare(strict_types=1);

namespace App\Model;

use Swagger\Annotations as SWG;

class Link extends Element
{
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
