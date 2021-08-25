<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\Cache;

use League\Flysystem\FileAttributes;

class FileMetadataCache
{
    private ?int $lastModified = null;
    private ?string $mimeType = null;
    private ?int $fileSize = null;
    private ?string $visibility = null;

    public function getLastModified(): ?int
    {
        return $this->lastModified;
    }

    public function setLastModified(int $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function setFromFileAttributes(FileAttributes $fileAttributes): self
    {
        if ($lastModifier = $fileAttributes->lastModified()) {
            $this->lastModified = $lastModifier;
        }

        if ($mimeType = $fileAttributes->mimeType()) {
            $this->mimeType = $mimeType;
        }

        if ($fileSize = $fileAttributes->fileSize()) {
            $this->fileSize = $fileSize;
        }

        if ($visibility = $fileAttributes->visibility()) {
            $this->visibility = $visibility;
        }

        return $this;
    }

    public function buildFileAttributes(string $path): FileAttributes
    {
        return new FileAttributes(
            $path,
            $this->fileSize,
            $this->visibility,
            $this->lastModified,
            $this->mimeType
        );
    }
}
