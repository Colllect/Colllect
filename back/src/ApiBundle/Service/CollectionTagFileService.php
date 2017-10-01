<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\Exception\FilesystemCannotWriteException;
use ApiBundle\Exception\TagAlreadyExistsException;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Model\Tag;
use ApiBundle\Util\CollectionPath;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CollectionTagFileService
{
    const TAGS_FILE = '.tags.collect';

    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var array
     */
    private $tagsFilesCache = [];


    public function __construct(TokenStorage $tokenStorage, FilesystemAdapterManager $flysystemAdapters)
    {
        $user = $tokenStorage->getToken()->getUser();

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
    }


    /**
     * Get all collection tags
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Tag[]
     */
    public function getAll(string $encodedCollectionPath): array
    {
        if (array_key_exists($encodedCollectionPath, $this->tagsFilesCache)) {
            return $this->tagsFilesCache[$encodedCollectionPath];
        }

        $tagsFilePath = $this->getTagsFilePath($encodedCollectionPath);

        // If tags file does not exists, return an empty array
        if (!$this->filesystem->has($tagsFilePath)) {
            $this->tagsFilesCache[$encodedCollectionPath] = [];
            return [];
        }

        $tagsFileContent = $this->filesystem->read($tagsFilePath);

        try {
            $flatTags = \GuzzleHttp\json_decode($tagsFileContent, true);
        } catch (\Exception $exception) {
            return [];
        }

        $tags = [];
        foreach ($flatTags as $flatTag) {
            $tags[] = new Tag($flatTag);
        }

        $this->tagsFilesCache[$encodedCollectionPath] = $tags;

        return $tags;
    }

    /**
     * Get a collection tag
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param string $tagName The searched tag name
     * @return Tag
     */
    public function get(string $encodedCollectionPath, string $tagName): Tag
    {
        $tags = $this->getAll($encodedCollectionPath);

        $filteredTags = array_filter($tags, function (Tag $tag) use ($tagName) {
            return $tag->getName() === $tagName;
        });

        if (count($filteredTags) === 0) {
            throw new NotFoundHttpException('error.tag_not_found');
        }

        // We don't want to keep reference
        $tag = clone array_shift($filteredTags);

        return $tag;
    }

    /**
     * @param string $encodedCollectionPath
     * @param Tag $tag
     * @throws TagAlreadyExistsException
     */
    public function add(string $encodedCollectionPath, Tag $tag)
    {
        // Check if tag does not already exists
        if ($this->has($encodedCollectionPath, $tag)) {
            throw new TagAlreadyExistsException();
        }

        // Add the tag to tag list
        $this->tagsFilesCache[$encodedCollectionPath][] = $tag;
    }

    /**
     * @param string $encodedCollectionPath
     * @param Tag $tag
     * @throws TagAlreadyExistsException
     */
    public function remove(string $encodedCollectionPath, Tag $tag)
    {
        // Check if tag does not already exists
        if (!$this->has($encodedCollectionPath, $tag)) {
            throw new NotFoundHttpException('error.tag_not_found');
        }

        // remove the tag from the list
        $this->tagsFilesCache[$encodedCollectionPath] = array_filter(
            $this->tagsFilesCache[$encodedCollectionPath],
            function (Tag $existingTag) use ($tag) {
                return $existingTag->getName() !== $tag->getName();
            }
        );
    }

    /**
     * @param string $encodedCollectionPath
     * @throws FilesystemCannotWriteException
     */
    public function save(string $encodedCollectionPath)
    {
        // Remap tag objects to flat array
        $flatTags = array_map(function (Tag $tag) {
            return [
                'name' => $tag->getName(),
            ];
        }, $this->getall($encodedCollectionPath));
        $tagsFileContent = \GuzzleHttp\json_encode($flatTags);

        $tagsFilePath = $this->getTagsFilePath($encodedCollectionPath);

        // Put new tag list info Collection tags file
        if (!$this->filesystem->put($tagsFilePath, $tagsFileContent)) {
            throw new FilesystemCannotWriteException();
        }
    }

    /**
     * Collection has tagName
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param Tag $tag Collection tag to find
     * @return bool
     */
    public function has(string $encodedCollectionPath, Tag $tag): bool
    {
        $tags = $this->getAll($encodedCollectionPath);

        $existingTagNames = array_map(function (Tag $tag) {
            return $tag->getName();
        }, $tags);

        $hasTag = in_array($tag->getName(), $existingTagNames);

        return $hasTag;
    }

    /**
     * Get tags file path from a collection
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return string Collection tags file path
     */
    private function getTagsFilePath(string $encodedCollectionPath): string
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);
        $tagsFilePath = $collectionPath . '/' . self::TAGS_FILE;

        return $tagsFilePath;
    }
}
