<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Exception\NotSupportedElementTypeException;
use AppBundle\FlysystemAdapter\FlysystemAdapterInterface;
use AppBundle\Form\Type\ElementType;
use AppBundle\Model\Element;
use AppBundle\Model\ElementFile;
use AppBundle\Util\Base64;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
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


    public function __construct(TokenStorage $tokenStorage, FlysystemAdapterInterface $flysystemAdapter, FormFactory $formFactory, ElementFileHandler $elementFileHandler)
    {
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $this->filesystem = $flysystemAdapter->getFilesystem($user);
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

        // Sort files by last updated date
        uasort($filesMetadata, function ($a, $b) {
            if ($a['timestamp'] == $b['timestamp']) {
                return 0;
            }

            return $a['timestamp'] < $b['timestamp'] ? -1 : 1;
        });

        // Get typed element for each file
        $elements = [];
        foreach ($filesMetadata as $fileMetadata) {
            try {
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
        $element = Element::get($meta);

        return $element;
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
}