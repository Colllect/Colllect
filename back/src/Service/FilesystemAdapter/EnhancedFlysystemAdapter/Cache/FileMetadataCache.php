<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\Cache;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;

class FileMetadataCache
{
    private ?string $type = null;
    private ?int $lastModified = null;
    private ?string $mimeType = null;
    private ?int $fileSize = null;
    private ?string $visibility = null;
    /** @var array<string>|null */
    private ?array $listContents = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

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

    /**
     * @return array<string>|null
     */
    public function getListContents(): ?array
    {
        return $this->listContents;
    }

    /**
     * @param array<string> $listContents
     */
    public function setListContents(array $listContents): self
    {
        $this->listContents = $listContents;

        return $this;
    }

    public function setFromFileAttributes(FileAttributes $fileAttributes): self
    {
        $this->type = StorageAttributes::TYPE_FILE;

        if ($lastModified = $fileAttributes->lastModified()) {
            $this->lastModified = $lastModified;
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

    public function setFromDirectoryAttributes(DirectoryAttributes $directoryAttributes): self
    {
        $this->type = StorageAttributes::TYPE_DIRECTORY;

        if ($lastModified = $directoryAttributes->lastModified()) {
            $this->lastModified = $lastModified;
        }

        if ($visibility = $directoryAttributes->visibility()) {
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

    public function buildStorageAttributes(string $path): DirectoryAttributes|FileAttributes
    {
        if ($this->type === StorageAttributes::TYPE_DIRECTORY) {
            return new DirectoryAttributes(
                $path,
                $this->visibility,
                $this->lastModified,
            );
        }

        return $this->buildFileAttributes($path);
    }
}
