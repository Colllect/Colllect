<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\NotSupportedElementTypeException;
use App\Model\Element\ElementInterface;
use App\Util\Base64;
use App\Util\ElementBasenameParser;
use App\Util\ElementRegistry;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ElementFile
{
    #[Assert\File]
    protected UploadedFile $file;

    #[Assert\Url]
    protected string $url;

    #[Assert\Type(type: 'string')]
    protected string $content;

    #[Assert\Type(type: 'string')]
    protected string $name;

    /**
     * @var array<string>
     */
    #[Assert\Type(type: 'array')]
    protected array $tags = [];

    #[Assert\Type(type: 'string')]
    protected string $extension;

    #[Assert\Type(type: 'string')]
    protected string $type;

    private string $basename;

    public function __construct(ElementInterface $element = null)
    {
        $this->tags = [];

        if ($element !== null) {
            $this->name = $element->getName();
            $this->tags = $element->getTags();
            $this->extension = $element->getExtension();
            $this->basename = Base64::decode($element->getEncodedElementBasename());
        }
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getBasename(): ?string
    {
        return $this->basename;
    }

    /**
     * @throws NotSupportedElementTypeException
     */
    public function setBasename(string $basename): self
    {
        $this->basename = $basename;

        // Remove illegal chars
        $basename = preg_replace('#[:;\[\]\/?]+#i', '', $basename);

        if ($basename === null) {
            throw new InvalidArgumentException('Invalid basename');
        }

        $elementMeta = ElementBasenameParser::parse($basename);

        $this->name = $elementMeta['name'];
        $this->tags = $elementMeta['tags'];
        $this->extension = $elementMeta['extension'];

        return $this;
    }

    public function getCleanedBasename(): ?string
    {
        if (!$this->name || !$this->extension) {
            return null;
        }

        $concatTags = implode(
            '',
            array_map(
                fn ($tag): string => ' #' . str_replace(' ', '_', $tag),
                $this->tags
            )
        );

        return $this->name . $concatTags . '.' . $this->extension;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Add a tag to element file.
     */
    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove a tag from element file.
     */
    public function removeTag(string $tag): self
    {
        $this->tags = array_filter(
            $this->tags,
            fn (string $existingTagName): bool => $tag !== $existingTagName
        );

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!ElementRegistry::isValidType($type)) {
            $supportedTypes = implode(', ', array_keys(ElementRegistry::getExtensionsByType()));
            throw new InvalidArgumentException('$type must be one of: ' . $supportedTypes . '. "' . $type . '" given.');
        }

        $this->type = $type;

        return $this;
    }
}
