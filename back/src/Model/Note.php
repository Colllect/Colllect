<?php

declare(strict_types=1);

namespace App\Model;

use Swagger\Annotations as SWG;

class Note extends Element
{
    /**
     * @var string
     *
     * @SWG\Property(type="string")
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
     * {@inheritdoc}
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }
}
