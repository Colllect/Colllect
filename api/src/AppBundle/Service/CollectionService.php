<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Exception\NotSupportedElementTypeException;
use AppBundle\FlysystemAdapter\FlysystemAdapterInterface;
use AppBundle\Form\Type\ElementType;
use AppBundle\Model\Element;
use AppBundle\Model\ElementFile;
use AppBundle\Util\Base64;
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
     * Base64 decode elementName to get basename from URL
     * Also throw error if elementName is invalid or not supported
     *
     * @param string $elementName Base 64 encoded filename
     * @param string $collectionPath Collection name
     * @return Element
     */
    public function getElementByEncodedElementName($elementName, $collectionPath)
    {
        if (!Base64::isValidBase64($elementName)) {
            throw new BadRequestHttpException("request.invalid_element_name");
        }

        $basename = base64_decode($elementName);

        // Check if file type is supported before call filesystem
        // Throw exception if element type is not supported
        Element::getTypeByPath($basename);

        $path = $collectionPath . '/' . $basename;

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
        $form = $this->formFactory->create(ElementType::class, $elementFile);

        $requestContent = $request->request->all();
        foreach ($request->files as $k => $requestFile) {
            $requestContent[$k] = $requestFile;
        }

        $form->submit($requestContent);

        if (!$form->isValid()) {
            return $form;
        }

        $this->elementFileHandler->handleFileElement($elementFile);

        $fullPath = $collectionPath . '/' . $elementFile->getBasename();
        $this->filesystem->write($fullPath, $elementFile->getContent());

        return [
            'basename' => $elementFile->getBasename(),
            'type' => $elementFile->getType(),
        ];
    }
}