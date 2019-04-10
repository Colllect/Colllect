<?php

declare(strict_types=1);

namespace App\Service;

use App\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Entity\User;
use App\Exception\FilesystemCannotRenameException;
use App\Exception\FilesystemCannotWriteException;
use App\Exception\NotSupportedElementTypeException;
use App\FilesystemAdapter\FilesystemAdapterManager;
use App\Form\ElementType;
use App\Model\Element;
use App\Model\ElementFile;
use App\Util\Base64;
use App\Util\ColllectionPath;
use App\Util\Metadata;
use Closure;
use DateTime;
use Exception;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Stopwatch\Stopwatch;

class ColllectionElementService
{
    /**
     * @var EnhancedFilesystemInterface
     */
    private $filesystem;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ElementFileHandler
     */
    private $elementFileHandler;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * ColllectionElementService constructor.
     *
     * @param Security                 $security
     * @param FilesystemAdapterManager $flysystemAdapters
     * @param FormFactoryInterface     $formFactory
     * @param ElementFileHandler       $elementFileHandler
     * @param Stopwatch                $stopwatch
     *
     * @throws Exception
     */
    public function __construct(
        Security $security,
        FilesystemAdapterManager $flysystemAdapters,
        FormFactoryInterface $formFactory,
        ElementFileHandler $elementFileHandler,
        Stopwatch $stopwatch
    ) {
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new Exception('$user must be instance of ' . User::class);
        }

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
        $this->formFactory = $formFactory;
        $this->elementFileHandler = $elementFileHandler;
        $this->stopwatch = $stopwatch;
    }

    /**
     * Get an array of typed elements from a colllection.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @return Element[]
     */
    public function list(string $encodedColllectionPath): array
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_list');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        try {
            $filesMetadata = $this->filesystem->listWith(['timestamp', 'size'], $colllectionPath);
        } catch (Exception $e) {
            // We can't catch 'not found'-like exception for each adapter,
            // so we normalize the result
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_element_list');
            }

            return [];
        }

        // Keep only files
        $filesMetadata = array_filter(
            $filesMetadata,
            function ($fileMetadata) {
                return $fileMetadata['type'] === 'file';
            }
        );

        if (\count($filesMetadata) > 0) {
            // Sort files by last updated date
            uasort(
                $filesMetadata,
                function ($a, $b) {
                    $aTimestamp = $a['timestamp'];
                    $bTimestamp = $b['timestamp'];

                    if ($aTimestamp === $bTimestamp) {
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
                $elements[] = Element::get($fileMetadata, $encodedColllectionPath);
            } catch (NotSupportedElementTypeException $e) {
                // Ignore not supported elements
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_element_list');
        }

        return $elements;
    }

    /**
     * Add an element to a colllection.
     *
     * @param string  $encodedColllectionPath Base 64 encoded colllection path
     * @param Request $request
     *
     * @return Element|FormInterface
     *
     * @throws FileNotFoundException
     * @throws FilesystemCannotWriteException
     * @throws NotSupportedElementTypeException
     * @throws FileExistsException
     * @throws Exception                        TODO: make a typed exception
     */
    public function create(string $encodedColllectionPath, Request $request)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_create');
        }

        $elementFile = new ElementFile();
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_element_create');
            }

            return $form;
        }

        $this->elementFileHandler->handleFileElement($elementFile);

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $colllectionPath . '/' . $elementFile->getCleanedBasename();
        if (!$this->filesystem->write($path, $elementFile->getContent())) {
            throw new FilesystemCannotWriteException();
        }

        $elementMetadata = $this->filesystem->getMetadata($path);
        $element = Element::get($elementMetadata, $encodedColllectionPath);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_element_create');
        }

        return $element;
    }

    /**
     * Update an element from a colllection.
     *
     * @param string  $encodedElementBasename Base 64 encoded basename
     * @param string  $encodedColllectionPath Base 64 encoded colllection path
     * @param Request $request
     *
     * @return Element|FormInterface
     *
     * @throws FileNotFoundException
     * @throws FilesystemCannotRenameException
     * @throws FilesystemCannotWriteException
     * @throws NotSupportedElementTypeException
     * @throws FileExistsException
     */
    public function update(string $encodedElementBasename, string $encodedColllectionPath, Request $request)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_update');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);
        $elementMetadata = $this->filesystem->getMetadata($path);
        $element = Element::get($elementMetadata, $encodedColllectionPath);

        $elementFile = new ElementFile($element);
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_element_update');
            }

            return $form;
        }

        $newPath = $colllectionPath . '/' . $elementFile->getCleanedBasename();

        // Rename if necessary
        if ($path !== $newPath) {
            if (!$this->filesystem->rename($path, $newPath)) {
                if ($this->stopwatch) {
                    $this->stopwatch->stop('colllection_element_update');
                }

                throw new FilesystemCannotRenameException();
            }
        }

        // Update content if necessary
        if ($element::shouldLoadContent() && (bool) $elementFile->getContent()) {
            if (!$this->filesystem->update($newPath, $elementFile->getContent())) {
                if ($this->stopwatch) {
                    $this->stopwatch->stop('colllection_element_update');
                }

                throw new FilesystemCannotWriteException();
            }
        }

        // Get fresh data about updated element
        $elementMetadata = $this->filesystem->getMetadata($newPath);
        $updatedElement = Element::get($elementMetadata, $encodedColllectionPath);

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_element_update');
        }

        return $updatedElement;
    }

    /**
     * Get an element from a colllection based on base 64 encoded basename.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @return Element
     *
     * @throws FileNotFoundException
     * @throws NotSupportedElementTypeException
     */
    public function get(string $encodedElementBasename, string $encodedColllectionPath): Element
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_get');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);

        $meta = $this->filesystem->getMetadata($path);

        if (!$meta) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_element_get');
            }

            throw new NotFoundHttpException('error.element_not_found');
        }

        $standardizedMeta = Metadata::standardize($meta, $path);
        $element = Element::get($standardizedMeta, $encodedColllectionPath);

        if ($element::shouldLoadContent()) {
            $content = $this->filesystem->read($path);
            $element->setContent($content);
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_element_get');
        }

        return $element;
    }

    /**
     * Get content of an element from a colllection based on base 64 encoded basename.
     *
     * @param string    $encodedElementBasename Base 64 encoded basename
     * @param string    $encodedColllectionPath Base 64 encoded colllection path
     * @param HeaderBag $requestHeaders
     *
     * @return Response
     *
     * @throws NotSupportedElementTypeException
     */
    public function getContent(
        string $encodedElementBasename,
        string $encodedColllectionPath,
        HeaderBag $requestHeaders
    ): Response {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_get_content');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);

        try {
            $meta = $this->filesystem->getMetadata($path);

            if (!$meta) {
                if ($this->stopwatch) {
                    $this->stopwatch->stop('colllection_element_get_content');
                }

                throw new NotFoundHttpException('error.element_not_found');
            }

            if ($requestHeaders->has('if-modified-since')) {
                $modified = (new DateTime($requestHeaders->get('if-modified-since')))->getTimestamp();
                if ($meta['timestamp'] <= $modified) {
                    $response = new Response();
                    $response->setStatusCode(Response::HTTP_NOT_MODIFIED);

                    if ($this->stopwatch) {
                        $this->stopwatch->stop('colllection_element_get_content');
                    }

                    return $response;
                }
            }

            $standardizedMeta = Metadata::standardize($meta, $path);

            $content = $this->filesystem->read($path);

            $response = new Response();
            $response->setContent($content);
            $response->headers->set('Content-Type', $standardizedMeta['mimetype']);
            $response->setLastModified((new DateTime())->setTimestamp($standardizedMeta['timestamp']));

            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_element_get_content');
            }

            return $response;
        } catch (FileNotFoundException $e) {
            if ($this->stopwatch) {
                $this->stopwatch->stop('colllection_element_get_content');
            }

            throw new NotFoundHttpException();
        }
    }

    /**
     * Delete an element from a colllection based on base 64 encoded basename.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @throws NotSupportedElementTypeException
     */
    public function delete(string $encodedElementBasename, string $encodedColllectionPath): void
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_delete');
        }

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);

        try {
            $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            // If file does not exists or was already deleted, it's OK
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_element_delete');
        }
    }

    /**
     * @param string  $encodedColllectionPath
     * @param Closure $matches                Should return true if the element need to be process
     * @param Closure $process                The process applied to element file
     *
     * @throws FileNotFoundException
     * @throws FileExistsException
     */
    public function batchRename(string $encodedColllectionPath, Closure $matches, Closure $process): void
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('colllection_element_batch_rename');
        }

        /** @var Element[] $elements */
        $elements = $this->list($encodedColllectionPath);
        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        foreach ($elements as $element) {
            if ($matches($element)) {
                $elementFile = new ElementFile($element);
                $path = $colllectionPath . '/' . $elementFile->getBasename();
                $process($elementFile);
                $newPath = $colllectionPath . '/' . $elementFile->getCleanedBasename();

                $this->filesystem->rename($path, $newPath);
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop('colllection_element_batch_rename');
        }
    }

    /**
     * Return file path by check and decode elementName.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $colllectionPath        Colllection path
     *
     * @return string
     *
     * @throws NotSupportedElementTypeException
     */
    private function getElementPath(string $encodedElementBasename, string $colllectionPath): string
    {
        if (!Base64::isValidBase64($encodedElementBasename)) {
            throw new BadRequestHttpException('request.badly_encoded_element_name');
        }

        $basename = Base64::decode($encodedElementBasename);

        // Check if file type is supported before call filesystem
        // Throw exception if element type is not supported
        Element::getTypeByPath($basename);

        $path = $colllectionPath . '/' . $basename;

        return $path;
    }

    /**
     * Update element file with request data.
     *
     * @param Request     $request
     * @param ElementFile $elementFile
     *
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
