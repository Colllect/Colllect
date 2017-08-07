<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\Exception\NotSupportedElementTypeException;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Form\Type\CollectionType;
use ApiBundle\Form\Type\ElementType;
use ApiBundle\Model\Collection;
use ApiBundle\Model\Element;
use ApiBundle\Model\ElementFile;
use ApiBundle\Util\Base64;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CollectionService
{
    const INBOX_FOLDER = 'Inbox';
    const COLLECTIONS_FOLDER = 'Collections';
    const VALID_FOLDERS = [self::INBOX_FOLDER, self::COLLECTIONS_FOLDER];

    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ElementFileHandler
     */
    private $elementFileHandler;


    public function __construct(TokenStorage $tokenStorage, FilesystemAdapterManager $flysystemAdapters, FormFactory $formFactory, ElementFileHandler $elementFileHandler)
    {
        $user = $tokenStorage->getToken()->getUser();

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
        $this->elementFileHandler = $elementFileHandler;
    }

    /**
     * Get an array of all user Collections
     *
     * @return Collection[]
     */
    public function list(): array
    {
        try {
            $collectionsMetadata = $this->filesystem->listContents(self::COLLECTIONS_FOLDER);
        } catch (\Exception $e) {
            // We can't catch "not found"-like exception for each adapter,
            // so we normalize the result
            return [];
        }

        if (count($collectionsMetadata) === 0) {
            return [];
        }

        // Get typed collection for each folder
        $collections = [];
        foreach ($collectionsMetadata as $collectionMetadata) {
            if ($collectionMetadata['type'] !== 'dir') {
                continue;
            }

            $collectionMetadata = $this->standardizeMetadata($collectionMetadata);
            $collections[] = new Collection($collectionMetadata);
        }

        // Sort collections by name
        uasort(
            $collections,
            function (Collection $a, Collection $b) {
                return $a->getName() < $b->getName() ? -1 : 1;
            }
        );

        return $collections;
    }

    /**
     * Create a collection
     *
     * @param Request $request
     * @return Collection|FormInterface
     */
    public function create(Request $request)
    {
        $collection = new Collection();
        $form = $this->formFactory->create(CollectionType::class, $collection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $path = self::COLLECTIONS_FOLDER . '/' . $collection->getName();
        $this->filesystem->createDir($path);

        return $collection;
    }

    /**
     * Get a collection by path
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Collection
     */
    public function get(string $encodedCollectionPath): Collection
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);

        $meta = $this->filesystem->getMetadata($collectionPath);
        $standardizedMeta = $this->standardizeMetadata($meta, $collectionPath);

        $collection = new Collection($standardizedMeta);

        return $collection;
    }

    /**
     * Update a collection by path
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param Request $request
     * @return Collection|FormInterface
     */
    public function update(string $encodedCollectionPath, Request $request)
    {
        $collection = $this->get($encodedCollectionPath);

        $renamedCollection = new Collection();
        $form = $this->formFactory->create(CollectionType::class, $renamedCollection);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $path = self::COLLECTIONS_FOLDER . '/' . $collection->getName();
        $renamedPath = self::COLLECTIONS_FOLDER . '/' . $renamedCollection->getName();
        $this->filesystem->renameDir($path, $renamedPath);

        return $renamedCollection;
    }

    /**
     * Delete a collection based on base 64 encoded basename
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     */
    public function delete(string $encodedCollectionPath)
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);

        try {
            $this->filesystem->deleteDir($collectionPath);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }
    }

    /**
     * Get an array of typed elements from a collection
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Element[]
     */
    public function listElements(string $encodedCollectionPath): array
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);
        try {
            $filesMetadata = $this->filesystem->listContents($collectionPath);
        } catch (\Exception $e) {
            // We can't catch "not found"-like exception for each adapter,
            // so we normalize the result
            return [];
        }

        if (count($filesMetadata) > 0 && isset($filesMetadata[0]['timestamp'])) {
            // Sort files by last updated date
            uasort(
                $filesMetadata,
                function ($a, $b) {
                    $aTimestamp = isset($a['timestamp']) ? $a['timestamp'] : -1;
                    $bTimestamp = isset($b['timestamp']) ? $b['timestamp'] : -1;

                    if ($aTimestamp == $bTimestamp) {
                        return 0;
                    }

                    return $aTimestamp < $bTimestamp ? -1 : 1;
                }
            );
        }

        // Get typed element for each file
        $elements = [];
        foreach ($filesMetadata as $fileMetadata) {
            try {
                $fileMetadata = $this->standardizeMetadata($fileMetadata);
                $elements[] = Element::get($fileMetadata);
            } catch (NotSupportedElementTypeException $e) {
                // Ignore not supported elements
            }
        }

        return $elements;
    }

    /**
     * Add an element to a collection
     *
     * @param Request $request
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Element|FormInterface
     */
    public function addElement(Request $request, string $encodedCollectionPath)
    {
        $elementFile = new ElementFile();
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            return $form;
        }

        $this->elementFileHandler->handleFileElement($elementFile);

        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);
        $path = $collectionPath . '/' . $elementFile->getBasename();
        $this->filesystem->write($path, $elementFile->getContent());

        $elementMetadata = $this->filesystem->getMetadata($path);
        $element = Element::get($elementMetadata);

        return $element;
    }

    /**
     * Update an element from a collection
     *
     * @param Request $request
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Element|FormInterface
     */
    public function updateElementByEncodedElementBasename(Request $request, string $encodedElementBasename, string $encodedCollectionPath)
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);

        $path = $this->getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath);
        $elementMetadata = $this->filesystem->getMetadata($path);
        $element = Element::get($elementMetadata);

        $elementFile = new ElementFile($element);
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            return $form;
        }

        $newPath = $collectionPath . '/' . $elementFile->getBasename();

        // Rename if necessary
        if ($path !== $newPath) {
            $this->filesystem->rename($path, $newPath);
        }

        // Update content if necessary
        if ($element->shouldLoadContent() && $element->getContent() !== $elementFile->getContent()) {
            $this->filesystem->write($newPath, $elementFile->getContent());
        }

        // Get fresh data about updated element
        $elementMetadata = $this->filesystem->getMetadata($newPath);
        $updatedElement = Element::get($elementMetadata);

        return $updatedElement;
    }

    /**
     * Get an element from a collection based on base 64 encoded basename
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Element
     */
    public function getElementByEncodedElementBasename(string $encodedElementBasename, string $encodedCollectionPath): Element
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);
        $path = $this->getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath);

        $meta = $this->filesystem->getMetadata($path);

        if (!$meta) {
            throw new NotFoundHttpException();
        }

        $standardizedMeta = $this->standardizeMetadata($meta, $path);
        $element = Element::get($standardizedMeta);

        if ($element->shouldLoadContent()) {
            $content = $this->filesystem->read($path);
            $element->setContent($content);
        }

        return $element;
    }

    /**
     * Get content of an element from a collection based on base 64 encoded basename
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Response
     */
    public function getElementContentResponseByEncodedElementBasename(string $encodedElementBasename, string $encodedCollectionPath): Response
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);
        $path = $this->getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath);

        $meta = $this->filesystem->getMetadata($path);
        $standardizedMeta = $this->standardizeMetadata($meta, $path);

        $content = $this->filesystem->read($path);

        $response = new Response();
        $response->setContent($content);
        if (isset($standardizedMeta['mimetype'])) {
            $response->headers->set('Content-Type', $standardizedMeta['mimetype']);
        }

        return $response;
    }

    /**
     * Delete an element from a collection based on base 64 encoded basename
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedCollectionPath Base 64 encoded collection path
     */
    public function deleteElementByEncodedElementBasename(string $encodedElementBasename, string $encodedCollectionPath)
    {
        $collectionPath = $this->decodeCollectionPath($encodedCollectionPath);
        $path = $this->getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath);

        try {
            $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }
    }

    /**
     * Return decoded collection path by check and decode collectionPath
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return string
     */
    private function decodeCollectionPath(string $encodedCollectionPath): string
    {
        if (!Base64::isValidBase64($encodedCollectionPath)) {
            throw new BadRequestHttpException("request.badly_encoded_collection_path");
        }

        $path = base64_decode($encodedCollectionPath);

        if (!in_array(explode('/', $path)[0], self::VALID_FOLDERS)) {
            throw new BadRequestHttpException("request.invalid_collection_path");
        }

        return $path;
    }

    /**
     * Return file path by check and decode elementName
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $collectionPath Collection path
     * @return string
     */
    private function getElementPathByEncodedElementBasename(string $encodedElementBasename, string $collectionPath): string
    {
        if (!Base64::isValidBase64($encodedElementBasename)) {
            throw new BadRequestHttpException("request.badly_encoded_element_name");
        }

        $basename = base64_decode($encodedElementBasename);

        // Check if file type is supported before call filesystem
        // Throw exception if element type is not supported
        Element::getTypeByPath($basename);

        $path = $collectionPath . '/' . $basename;

        return $path;
    }

    /**
     * Update element file with request data
     *
     * @param Request $request
     * @param ElementFile $elementFile
     * @return FormInterface
     */
    private function handleRequest(Request $request, ElementFile $elementFile): FormInterface
    {
        $form = $this->formFactory->create(ElementType::class, $elementFile);

        $requestContent = $request->request->all();
        foreach ($request->files as $k => $requestFile) {
            $requestContent[$k] = $requestFile;
        }

        $form->submit($requestContent, false);

        return $form;
    }

    /**
     * Standardize metadata format
     *
     * @param array $meta
     * @param string $path
     * @return array
     */
    private function standardizeMetadata(array $meta, string $path = null): array
    {
        // Add path if needed because some adapters didn't return it in metadata
        if ($path && !isset($meta['path'])) {
            $meta['path'] = $path;
        }

        // Set timestamp to -1 if needed because some adapters didn't return it in metadata
        if (!isset($meta['timestamp'])) {
            $meta['timestamp'] = -1;
        }

        return $meta;
    }
}
