<?php

namespace ApiBundle\Service;

use ApiBundle\Exception\NotSupportedElementTypeException;
use ApiBundle\FlysystemAdapter\FlysystemAdapters;
use ApiBundle\Form\Type\ElementType;
use ApiBundle\Model\Element;
use ApiBundle\Model\ElementFile;
use ApiBundle\Util\Base64;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CollectionService
{
    const INBOX_FOLDER = 'Inbox';

    /**
     * @var FilesystemInterface
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


    public function __construct(TokenStorage $tokenStorage, FlysystemAdapters $flysystemAdapters, FormFactory $formFactory, ElementFileHandler $elementFileHandler)
    {
        $user = $tokenStorage->getToken()->getUser();
        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
        $this->elementFileHandler = $elementFileHandler;
    }

    /**
     * Get an array of typed elements from a collection
     *
     * @param $collectionPath
     * @return Element[]
     */
    public function listElements($collectionPath)
    {
        $filesMetadata = $this->filesystem->listContents($collectionPath);

        if (count($filesMetadata) > 0 && isset($filesMetadata[0]['timestamp'])) {
            // Sort files by last updated date
            uasort(
                $filesMetadata,
                function ($a, $b) {
                    if ($a['timestamp'] == $b['timestamp']) {
                        return 0;
                    }

                    return $a['timestamp'] < $b['timestamp'] ? -1 : 1;
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
     * Get an element from a collection based on base 64 encoded basename
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $collectionPath Collection name
     * @return Element
     */
    public function getElementByEncodedElementBasename($encodedElementBasename, $collectionPath)
    {
        $path = $this->getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath);

        $meta = $this->filesystem->getMetadata($path);
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
     * @param string $collectionPath Collection name
     * @return Response
     */
    public function getElementContentResponseByEncodedElementBasename($encodedElementBasename, $collectionPath)
    {
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
     * Add an element to a collection
     *
     * @param Request $request
     * @param string $collectionPath
     * @return array|\Symfony\Component\Form\FormInterface
     */
    public function addElement(Request $request, $collectionPath)
    {
        $elementFile = new ElementFile();
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            return $form;
        }

        $this->elementFileHandler->handleFileElement($elementFile);

        $path = $collectionPath . '/' . $elementFile->getBasename();
        $this->filesystem->write($path, $elementFile->getContent());

        return [
            'basename' => $elementFile->getBasename(),
            'type' => $elementFile->getType(),
        ];
    }

    /**
     * Delete an element from a collection based on base 64 encoded basename
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $collectionPath Collection name
     */
    public function deleteElementByEncodedElementBasename($encodedElementBasename, $collectionPath)
    {
        $path = $this->getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath);

        try {
            $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }
    }

    /**
     * Return file path by check and decode elementName
     *
     * @param $encodedElementBasename
     * @param $collectionPath
     * @return string
     */
    private function getElementPathByEncodedElementBasename($encodedElementBasename, $collectionPath)
    {
        if (!Base64::isValidBase64($encodedElementBasename)) {
            throw new BadRequestHttpException("request.invalid_element_name");
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
     * @return \Symfony\Component\Form\FormInterface
     */
    private function handleRequest(Request $request, ElementFile $elementFile)
    {
        $form = $this->formFactory->create(ElementType::class, $elementFile);

        $requestContent = $request->request->all();
        foreach ($request->files as $k => $requestFile) {
            $requestContent[$k] = $requestFile;
        }

        $form->submit($requestContent);

        return $form;
    }

    /**
     * Standardize metadata format
     *
     * @param array $meta
     * @param string $path
     * @return array
     */
    private function standardizeMetadata($meta, $path = null)
    {
        // Add path if needed because some adapters didn't return it in metadata
        if ($path && !isset($meta['path'])) {
            $meta['path'] = $path;
        }

        // Set timestamp to 0 if needed because some adapters didn't return it in metadata
        if (!isset($meta['timestamp'])) {
            $meta['timestamp'] = null;
        }

        return $meta;
    }
}
