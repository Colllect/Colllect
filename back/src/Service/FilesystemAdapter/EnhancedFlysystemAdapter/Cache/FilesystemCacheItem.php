<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class FilesystemCacheItem
{
    private CacheItemPoolInterface $cachePool;
    private CacheItemInterface $item;
    private string $path;
    private ?FileMetadataCache $metadata = null;
    private bool $isHit;

    public function __construct(
        CacheItemPoolInterface $cachePool,
        CacheItemInterface $item,
        string $path
    ) {
        $this->cachePool = $cachePool;
        $this->item = $item;
        $this->path = $path;
        $this->isHit = $item->isHit();
    }

    public function exists(): bool
    {
        return $this->isHit;
    }

    public function initialize(): self
    {
        $this->metadata = new FileMetadataCache();

        return $this;
    }

    public function load(): self
    {
        $this->metadata = $this->item->get();

        return $this;
    }

    public function loadOrInitialize(): self
    {
        if ($this->exists()) {
            return $this->load();
        }

        return $this->initialize();
    }

    public function save(): self
    {
        $this->item->set($this->metadata);
        $this->cachePool->save($this->item);
        $this->isHit = true;

        return $this;
    }

    public function delete(): self
    {
        try {
            $this->cachePool->deleteItem($this->item->getKey());
            $this->isHit = false;
            unset($this->metadata);
        } catch (InvalidArgumentException $exception) {
            throw InvalidCacheItemException::withPathAndKey($this->path, $this->item->getKey());
        }

        return $this;
    }

    public function getMetadata(): FileMetadataCache
    {
        if (!isset($this->metadata)) {
            $this->loadOrInitialize();
        }

        return $this->metadata;
    }

    public function setMetadata(FileMetadataCache $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }
}
