<?php

namespace ApiBundle\Service;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\Exception\FilesystemCannotRenameException;
use ApiBundle\Exception\FilesystemCannotWriteException;
use ApiBundle\Exception\NotSupportedElementTypeException;
use ApiBundle\FilesystemAdapter\FilesystemAdapterManager;
use ApiBundle\Form\Type\ElementType;
use ApiBundle\Model\Element;
use ApiBundle\Model\ElementFile;
use ApiBundle\Util\Base64;
use ApiBundle\Util\CollectionPath;
use ApiBundle\Util\Metadata;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CollectionElementService
{
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
     * Get an array of typed elements from a collection
     *
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @return Element[]
     */
    public function list(string $encodedCollectionPath): array
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);
        try {
            $filesMetadata = $this->filesystem->listContents($collectionPath);
        } catch (\Exception $e) {
            // We can't catch 'not found'-like exception for each adapter,
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
                $fileMetadata = Metadata::standardize($fileMetadata);
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
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param Request $request
     * @return Element|FormInterface
     * @throws FilesystemCannotWriteException
     */
    public function create(string $encodedCollectionPath, Request $request)
    {
        $elementFile = new ElementFile();
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            return $form;
        }

        $this->elementFileHandler->handleFileElement($elementFile);

        $collectionPath = CollectionPath::decode($encodedCollectionPath);
        $path = $collectionPath . '/' . $elementFile->getBasename();
        if (!$this->filesystem->write($path, $elementFile->getContent())) {
            throw new FilesystemCannotWriteException();
        }

        $elementMetadata = $this->filesystem->getMetadata($path);
        $element = Element::get($elementMetadata);

        return $element;
    }

    /**
     * Update an element from a collection
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedCollectionPath Base 64 encoded collection path
     * @param Request $request
     * @return Element|FormInterface
     * @throws FilesystemCannotRenameException
     * @throws FilesystemCannotWriteException
     */
    public function update(string $encodedElementBasename, string $encodedCollectionPath, Request $request)
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);

        $path = $this->getElementPath($encodedElementBasename, $collectionPath);
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
            if (!$this->filesystem->rename($path, $newPath)) {
                throw new FilesystemCannotRenameException();
            }
        }

        // Update content if necessary
        if ($element->shouldLoadContent() && !!$elementFile->getContent()) {
            if (!$this->filesystem->update($newPath, $elementFile->getContent())) {
                throw new FilesystemCannotWriteException();
            }
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
    public function get(string $encodedElementBasename, string $encodedCollectionPath): Element
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);
        $path = $this->getElementPath($encodedElementBasename, $collectionPath);

        $meta = $this->filesystem->getMetadata($path);

        if (!$meta) {
            throw new NotFoundHttpException('error.element_not_found');
        }

        $standardizedMeta = Metadata::standardize($meta, $path);
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
    public function getContent(string $encodedElementBasename, string $encodedCollectionPath): Response
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);
        $path = $this->getElementPath($encodedElementBasename, $collectionPath);

        $meta = $this->filesystem->getMetadata($path);
        $standardizedMeta = Metadata::standardize($meta, $path);

        $content = $this->filesystem->read($path);

        $response = new Response();
        $response->setContent($content);
        $response->headers->set('Content-Type', $standardizedMeta['mimetype']);

        return $response;
    }

    /**
     * Delete an element from a collection based on base 64 encoded basename
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedCollectionPath Base 64 encoded collection path
     */
    public function delete(string $encodedElementBasename, string $encodedCollectionPath)
    {
        $collectionPath = CollectionPath::decode($encodedCollectionPath);
        $path = $this->getElementPath($encodedElementBasename, $collectionPath);

        try {
            $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }
    }

    /**
     * Return file path by check and decode elementName
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $collectionPath Collection path
     * @return string
     */
    private function getElementPath(string $encodedElementBasename, string $collectionPath): string
    {
        if (!Base64::isValidBase64($encodedElementBasename)) {
            throw new BadRequestHttpException('request.badly_encoded_element_name');
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
}
