<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\Cache;

use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToReadFile;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class EnhancedCacheAdapter implements EnhancedFlysystemAdapterInterface
{
    private EnhancedFlysystemAdapterInterface $adapter;
    private CacheItemPoolInterface $cachePool;
    /** @var array<FilesystemCacheItem> */
    private array $cacheItems = [];

    public function __construct(
        EnhancedFlysystemAdapterInterface $adapter,
        CacheItemPoolInterface $cachePool
    ) {
        $this->adapter = $adapter;
        $this->cachePool = $cachePool;
    }

    private function getCacheItem(string $path): FilesystemCacheItem
    {
        if (isset($this->cacheItems[$path])) {
            return $this->cacheItems[$path];
        }

        $key = hash('md4', $path);
        try {
            return $this->cacheItems[$path] = new FilesystemCacheItem(
                $this->cachePool,
                $this->cachePool->getItem($key),
                $path
            );
        } catch (InvalidArgumentException $exception) {
            throw InvalidCacheItemException::withPathAndKey($path, $key);
        }
    }

    public function fileExists(string $path): bool
    {
        $item = $this->getCacheItem($path);
        if ($item->exists()) {
            return true;
        }
        $fileExists = $this->adapter->fileExists($path);
        if ($fileExists) {
            $item->initialize()->save();
        }

        return $fileExists;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->adapter->write($path, $contents, $config);

        $item = $this->getCacheItem($path)->initialize();
        $metadata = $item->getMetadata();
        $metadata->setLastModified(time());
        if ($visibility = $config->get(Config::OPTION_VISIBILITY)) {
            $metadata->setVisibility($config->get(Config::OPTION_VISIBILITY));
        }
        $item->save();
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->adapter->writeStream($path, $contents, $config);

        $item = $this->getCacheItem($path)->initialize();
        $metadata = $item->getMetadata();
        $metadata->setLastModified(time());
        if ($visibility = $config->get(Config::OPTION_VISIBILITY)) {
            $metadata->setVisibility($config->get(Config::OPTION_VISIBILITY));
        }
        $item->save();
    }

    public function read(string $path): string
    {
        $item = $this->getCacheItem($path);
        try {
            $contents = $this->adapter->read($path);

            if (!$item->exists()) {
                $item->initialize()->save();
            }

            return $contents;
        } catch (UnableToReadFile $exception) {
            if ($item->exists()) {
                $item->delete();
            }

            throw $exception;
        }
    }

    public function readStream(string $path)
    {
        $item = $this->getCacheItem($path);
        try {
            $contents = $this->adapter->readStream($path);

            if (!$item->exists()) {
                $item->initialize()->save();
            }

            return $contents;
        } catch (UnableToReadFile $exception) {
            if ($item->exists()) {
                $item->delete();
            }

            throw $exception;
        }
    }

    public function delete(string $path): void
    {
        $this->adapter->delete($path);

        $item = $this->getCacheItem($path);
        if ($item->exists()) {
            $item->delete();
        }
    }

    public function deleteDirectory(string $path): void
    {
        /** @var StorageAttributes $storageAttributes */
        foreach ($this->adapter->listContents($path, true) as $storageAttributes) {
            if ($storageAttributes->isFile()) {
                $item = $this->getCacheItem($storageAttributes->path());
                if ($item->exists()) {
                    $item->delete();
                }
            }
        }

        $this->adapter->deleteDirectory($path);
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->adapter->createDirectory($path, $config);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->adapter->setVisibility($path, $visibility);

        $item = $this->getCacheItem($path);
        $item->getMetadata()->setVisibility($visibility);
        $item->save();
    }

    public function visibility(string $path): FileAttributes
    {
        $item = $this->getCacheItem($path);
        $metadata = $item->getMetadata();
        if ($metadata->getVisibility() !== null) {
            return $metadata->buildFileAttributes($path);
        }

        $fileAttributes = $this->adapter->visibility($path);

        $metadata->setFromFileAttributes($fileAttributes);
        $item->save();

        return $fileAttributes;
    }

    public function mimeType(string $path): FileAttributes
    {
        $item = $this->getCacheItem($path);
        $metadata = $item->getMetadata();
        if ($metadata->getMimeType() !== null) {
            return $metadata->buildFileAttributes($path);
        }

        $fileAttributes = $this->adapter->mimeType($path);

        $metadata->setFromFileAttributes($fileAttributes);
        $item->save();

        return $fileAttributes;
    }

    public function lastModified(string $path): FileAttributes
    {
        $item = $this->getCacheItem($path);
        $metadata = $item->getMetadata();
        if ($metadata->getLastModified() !== null) {
            return $metadata->buildFileAttributes($path);
        }

        $fileAttributes = $this->adapter->lastModified($path);

        $metadata->setFromFileAttributes($fileAttributes);
        $item->save();

        return $fileAttributes;
    }

    public function fileSize(string $path): FileAttributes
    {
        $item = $this->getCacheItem($path);
        $metadata = $item->getMetadata();
        if ($metadata->getFileSize() !== null) {
            return $metadata->buildFileAttributes($path);
        }

        $fileAttributes = $this->adapter->fileSize($path);

        $metadata->setFromFileAttributes($fileAttributes);
        $item->save();

        return $fileAttributes;
    }

    public function listContents(string $path, bool $deep): iterable
    {
        /** @var FileAttributes $storageAttributes */
        foreach ($this->adapter->listContents($path, $deep) as $storageAttributes) {
            if ($storageAttributes->isFile()) {
                $item = $this->getCacheItem($storageAttributes->path());
                $item->getMetadata()->setFromFileAttributes($storageAttributes);
                $item->save();
            }

            yield $storageAttributes;
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->adapter->move($source, $destination, $config);

        $from = $this->getCacheItem($source);
        if ($from->exists()) {
            $to = $this->getCacheItem($destination);
            $to->initialize()->setMetadata($from->load()->getMetadata())->save();
            $from->delete();
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->adapter->copy($source, $destination, $config);

        $from = $this->getCacheItem($source);
        if ($from->exists()) {
            $to = $this->getCacheItem($destination);
            $to->initialize()->setMetadata($from->load()->getMetadata())->save();
        }
    }

    public function renameDir(string $path, string $newPath, Config $config): void
    {
        $this->adapter->renameDir($path, $newPath, $config);

        $from = $this->getCacheItem($path);
        if ($from->exists()) {
            $to = $this->getCacheItem($newPath);
            $to->initialize()->setMetadata($from->load()->getMetadata())->save();
            $from->delete();
        }
    }
}
