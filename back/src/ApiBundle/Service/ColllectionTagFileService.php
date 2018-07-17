<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\Exception\FilesystemCannotWriteException;
use ApiBundle\Exception\TagAlreadyExistsException;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Model\Tag;
use ApiBundle\Util\ColllectionPath;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ColllectionTagFileService
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
     * Get all colllection tags
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @return Tag[]
     */
    public function getAll(string $encodedColllectionPath): array
    {
        if (array_key_exists($encodedColllectionPath, $this->tagsFilesCache)) {
            return $this->tagsFilesCache[$encodedColllectionPath];
        }

        $tagsFilePath = $this->getTagsFilePath($encodedColllectionPath);

        // If tags file does not exists, return an empty array
        if (!$this->filesystem->has($tagsFilePath)) {
            $this->tagsFilesCache[$encodedColllectionPath] = [];
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

        $this->tagsFilesCache[$encodedColllectionPath] = $tags;

        return $tags;
    }

    /**
     * Get a colllection tag
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param string $tagName The searched tag name
     * @return Tag
     */
    public function get(string $encodedColllectionPath, string $tagName): Tag
    {
        $tags = $this->getAll($encodedColllectionPath);

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
     * @param string $encodedColllectionPath
     * @param Tag $tag
     * @throws TagAlreadyExistsException
     */
    public function add(string $encodedColllectionPath, Tag $tag)
    {
        // Check if tag does not already exists
        if ($this->has($encodedColllectionPath, $tag)) {
            throw new TagAlreadyExistsException();
        }

        // Add the tag to tag list
        $this->tagsFilesCache[$encodedColllectionPath][] = $tag;
    }

    /**
     * @param string $encodedColllectionPath
     * @param Tag $tag
     * @throws TagAlreadyExistsException
     */
    public function remove(string $encodedColllectionPath, Tag $tag)
    {
        // Check if tag does not already exists
        if (!$this->has($encodedColllectionPath, $tag)) {
            throw new NotFoundHttpException('error.tag_not_found');
        }

        // remove the tag from the list
        $this->tagsFilesCache[$encodedColllectionPath] = array_filter(
            $this->tagsFilesCache[$encodedColllectionPath],
            function (Tag $existingTag) use ($tag) {
                return $existingTag->getName() !== $tag->getName();
            }
        );
    }

    /**
     * @param string $encodedColllectionPath
     * @throws FilesystemCannotWriteException
     */
    public function save(string $encodedColllectionPath)
    {
        // Remap tag objects to flat array
        $flatTags = array_map(function (Tag $tag) {
            return [
                'name' => $tag->getName(),
            ];
        }, $this->getall($encodedColllectionPath));
        $tagsFileContent = \GuzzleHttp\json_encode($flatTags);

        $tagsFilePath = $this->getTagsFilePath($encodedColllectionPath);

        // Put new tag list info Colllection tags file
        if (!$this->filesystem->put($tagsFilePath, $tagsFileContent)) {
            throw new FilesystemCannotWriteException();
        }
    }

    /**
     * Colllection has tagName
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @param Tag $tag Colllection tag to find
     * @return bool
     */
    public function has(string $encodedColllectionPath, Tag $tag): bool
    {
        $tags = $this->getAll($encodedColllectionPath);

        $existingTagNames = array_map(function (Tag $tag) {
            return $tag->getName();
        }, $tags);

        $hasTag = in_array($tag->getName(), $existingTagNames);

        return $hasTag;
    }

    /**
     * Get tags file path from a colllection
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     * @return string Colllection tags file path
     */
    private function getTagsFilePath(string $encodedColllectionPath): string
    {
        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $tagsFilePath = $colllectionPath . '/' . self::TAGS_FILE;

        return $tagsFilePath;
    }
}
