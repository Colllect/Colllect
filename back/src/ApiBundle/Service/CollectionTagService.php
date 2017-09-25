<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\Exception\FilesystemCannotWriteException;
use ApiBundle\Exception\TagAlreadyExistsException;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Form\Type\TagType;
use ApiBundle\Model\Tag;
use ApiBundle\Util\Base64;
use ApiBundle\Util\CollectionPath;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CollectionTagService
{
    const TAGS_FILE = '.tags.json';

    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var FormFactory
     */
    private $formFactory;


    public function __construct(TokenStorage $tokenStorage, FilesystemAdapterManager $flysystemAdapters, FormFactory $formFactory)
    {
        $user = $tokenStorage->getToken()->getUser();

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
    }

    /**
     * Get an array of tags from a collection
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Tag[]
     */
    public function list(string $encodedCollectionPath): array
    {
        $tagsFilePath = $this->getTagsFilePath($encodedCollectionPath);

        // If tags file does not exists, return an array
        if (!$this->filesystem->has($tagsFilePath)) {
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

        return $tags;
    }

    /**
     * Add a tag to a collection
     *
     * @param Request $request
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Tag|FormInterface
     * @throws FilesystemCannotWriteException
     * @throws TagAlreadyExistsException
     */
    public function create(Request $request, string $encodedCollectionPath)
    {
        $tag = new Tag();
        $form = $this->formFactory->create(TagType::class, $tag);
        $requestContent = $request->request->all();
        $form->submit($requestContent, false);

        if (!$form->isValid()) {
            return $form;
        }

        // Check if tag does not already exists
        $tags = $this->list($encodedCollectionPath);
        if ($this->has($tags, $tag->getName())) {
            throw new TagAlreadyExistsException();
        }

        // Add the tag to tag list
        $tags[] = $tag;

        // Remap tag objects to flat array
        $flatTags = array_map(function(Tag $tag) {
            return [
                'name' => $tag->getName(),
            ];
        }, $tags);
        $tagsFileContent = \GuzzleHttp\json_encode($flatTags);

        $tagsFilePath = $this->getTagsFilePath($encodedCollectionPath);

        // Put new tag list info Collection tags file
        if (!$this->filesystem->put($tagsFilePath, $tagsFileContent)) {
            throw new FilesystemCannotWriteException();
        }

        return $tag;
    }

    /**
     * Get a tag from a collection
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param string $encodedTagName Base 64 encoded tag name
     * @return Tag
     */
    public function get(string $encodedCollectionPath, string $encodedTagName): Tag
    {
        if (!Base64::isValidBase64($encodedTagName)) {
            throw new BadRequestHttpException('request.badly_encoded_tag_name');
        }
        $tagName = base64_decode(urldecode($encodedTagName));

        /** @var Tag[] $tags */
        $tags = $this->list($encodedCollectionPath);

        $filteredTags = array_filter($tags, function(Tag $tag) use ($tagName) {
            return $tag->getName() === $tagName;
        });

        if (count($filteredTags) === 0) {
            throw new NotFoundHttpException('error.tag_not_found');
        }

        $tag = array_shift($filteredTags);

        return $tag;
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

    /**
     * Collection has tagName
     *
     * @param array $tags Collection tags list
     * @param string $tagName Collection tag to find
     * @return bool
     */
    private function has(array $tags, string $tagName): bool
    {
        $existingTagNames = array_map(function ($tag) {
            return $tag['name'];
        }, $tags);

        return in_array($tagName, $existingTagNames);
    }
}
