<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\EmptyFileException;
use App\Exception\FilesystemCannotRenameException;
use App\Exception\FilesystemCannotWriteException;
use App\Exception\InvalidElementLinkException;
use App\Exception\NotSupportedElementTypeException;
use App\Form\ElementType;
use App\Model\Element\AbstractElement;
use App\Model\Element\ElementInterface;
use App\Model\ElementFile;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\FilesystemAdapterManager;
use App\Util\Base64;
use App\Util\ColllectionPath;
use App\Util\ElementBasenameParser;
use App\Util\Metadata;
use Closure;
use DateTime;
use Exception;
use InvalidArgumentException;
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
    private EnhancedFilesystemInterface $filesystem;

    /**
     * ColllectionElementService constructor.
     *
     * @throws Exception
     */
    public function __construct(
        private ElementFileHandler $elementFileHandler,
        private FormFactoryInterface $formFactory,
        private ?Stopwatch $stopwatch,
        FilesystemAdapterManager $flysystemAdapters,
        Security $security,
    ) {
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new InvalidArgumentException('$user must be instance of ' . User::class);
        }

        $this->filesystem = $flysystemAdapters->getFilesystem($user);
    }

    /**
     * Get an array of typed elements from a colllection.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @return ElementInterface[]
     */
    public function list(string $encodedColllectionPath): array
    {
        $this->stopwatch?->start('colllection_element_list');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        try {
            $filesMetadata = $this->filesystem->listWith(['timestamp', 'size'], $colllectionPath);
        } catch (Exception) {
            // We can't catch 'not found'-like exception for each adapter,
            // so we normalize the result
            $this->stopwatch?->stop('colllection_element_list');

            return [];
        }

        // Keep only files
        $filesMetadata = array_filter(
            $filesMetadata,
            fn ($fileMetadata) => $fileMetadata['type'] === 'file'
        );

        if (\count($filesMetadata) === 0) {
            $this->stopwatch?->stop('colllection_element_list');

            return [];
        }

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

        // Get typed element for each file
        $elements = [];
        foreach ($filesMetadata as $fileMetadata) {
            try {
                $fileMetadata = Metadata::standardize($fileMetadata);
                $element = AbstractElement::get($fileMetadata, $encodedColllectionPath);
                $elements[] = $element;
            } catch (NotSupportedElementTypeException) {
                // Ignore not supported elements
            }
        }

        $this->stopwatch?->stop('colllection_element_list');

        return $elements;
    }

    /**
     * Add an element to a colllection.
     *
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @throws EmptyFileException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FilesystemCannotWriteException
     * @throws InvalidElementLinkException
     * @throws NotSupportedElementTypeException
     */
    public function create(string $encodedColllectionPath, Request $request): ElementInterface|FormInterface
    {
        $this->stopwatch?->start('colllection_element_create');

        $elementFile = new ElementFile();
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            $this->stopwatch?->stop('colllection_element_create');

            return $form;
        }

        $this->elementFileHandler->handleFileElement($elementFile);

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $colllectionPath . '/' . $elementFile->getCleanedBasename();
        $content = $elementFile->getContent();
        if ($content === null) {
            throw new EmptyFileException();
        }
        if (!$this->filesystem->write($path, $content)) {
            throw new FilesystemCannotWriteException();
        }

        $elementMetadata = $this->filesystem->getMetadata($path);
        if ($elementMetadata === false) {
            throw new NotFoundHttpException('error.element_not_found');
        }
        $element = AbstractElement::get($elementMetadata, $encodedColllectionPath);

        $this->stopwatch?->stop('colllection_element_create');

        return $element;
    }

    /**
     * Update an element from a colllection.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FilesystemCannotRenameException
     * @throws NotSupportedElementTypeException
     */
    public function update(string $encodedElementBasename, string $encodedColllectionPath, Request $request): ElementInterface|FormInterface
    {
        $this->stopwatch?->start('colllection_element_update');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);

        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);
        $elementMetadata = $this->filesystem->getMetadata($path);
        if ($elementMetadata === false) {
            throw new NotFoundHttpException('error.element_not_found');
        }
        $element = AbstractElement::get($elementMetadata, $encodedColllectionPath);

        $elementFile = new ElementFile($element);
        $form = $this->handleRequest($request, $elementFile);

        if (!$form->isValid()) {
            $this->stopwatch?->stop('colllection_element_update');

            return $form;
        }

        $newPath = $colllectionPath . '/' . $elementFile->getCleanedBasename();

        // Rename if necessary
        if ($path !== $newPath) {
            if (!$this->filesystem->rename($path, $newPath)) {
                $this->stopwatch?->stop('colllection_element_update');

                throw new FilesystemCannotRenameException();
            }
        }

        // Update content if necessary
        if ($element::shouldLoadContent()) {
            $content = $elementFile->getContent();
            if ($content !== null) {
                if (!$this->filesystem->update($newPath, $content)) {
                    $this->stopwatch?->stop('colllection_element_update');

                    throw new NotFoundHttpException('error.element_not_found');
                }
            }
        }

        // Get fresh data about updated element
        $elementMetadata = $this->filesystem->getMetadata($newPath);
        if ($elementMetadata === false) {
            throw new FileNotFoundException($path);
        }
        $updatedElement = AbstractElement::get($elementMetadata, $encodedColllectionPath);

        $this->stopwatch?->stop('colllection_element_update');

        return $updatedElement;
    }

    /**
     * Get an element from a colllection based on base 64 encoded basename.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @throws FileNotFoundException
     * @throws NotSupportedElementTypeException
     */
    public function get(string $encodedElementBasename, string $encodedColllectionPath): ElementInterface
    {
        $this->stopwatch?->start('colllection_element_get');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);

        $elementMetadata = $this->filesystem->getMetadata($path);
        if ($elementMetadata === false) {
            $this->stopwatch?->stop('colllection_element_get');

            throw new NotFoundHttpException('error.element_not_found');
        }

        $standardizedMeta = Metadata::standardize($elementMetadata, $path);
        $element = AbstractElement::get($standardizedMeta, $encodedColllectionPath);

        if ($element::shouldLoadContent()) {
            $content = $this->filesystem->read($path);
            if ($content === false) {
                $this->stopwatch?->stop('colllection_element_get');

                throw new NotFoundHttpException('error.element_not_found');
            }
            $element->setContent($content);
        }

        $this->stopwatch?->stop('colllection_element_get');

        return $element;
    }

    /**
     * Get content of an element from a colllection based on base 64 encoded basename.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $encodedColllectionPath Base 64 encoded colllection path
     *
     * @throws NotSupportedElementTypeException
     * @throws Exception
     */
    public function getContent(
        string $encodedElementBasename,
        string $encodedColllectionPath,
        HeaderBag $requestHeaders
    ): Response {
        $this->stopwatch?->start('colllection_element_get_content');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);

        try {
            $meta = $this->filesystem->getMetadata($path);

            if (!$meta) {
                $this->stopwatch?->stop('colllection_element_get_content');

                throw new NotFoundHttpException('error.element_not_found');
            }

            if ($requestHeaders->has('if-modified-since')) {
                $modified = (new DateTime($requestHeaders->get('if-modified-since') ?? 'now'))->getTimestamp();
                if ($meta['timestamp'] <= $modified) {
                    $response = new Response();
                    $response->setStatusCode(Response::HTTP_NOT_MODIFIED);

                    $this->stopwatch?->stop('colllection_element_get_content');

                    return $response;
                }
            }

            $standardizedMeta = Metadata::standardize($meta, $path);

            $content = $this->filesystem->read($path);

            $response = new Response();
            $response->setContent($content);
            $response->headers->set('Content-Type', (string) $standardizedMeta['mimetype']);
            $response->setLastModified((new DateTime())->setTimestamp((int) $standardizedMeta['timestamp']));

            $this->stopwatch?->stop('colllection_element_get_content');

            return $response;
        } catch (FileNotFoundException) {
            $this->stopwatch?->stop('colllection_element_get_content');

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
        $this->stopwatch?->start('colllection_element_delete');

        $colllectionPath = ColllectionPath::decode($encodedColllectionPath);
        $path = $this->getElementPath($encodedElementBasename, $colllectionPath);

        try {
            $this->filesystem->delete($path);
        } catch (FileNotFoundException) {
            // If file does not exists or was already deleted, it's OK
        }

        $this->stopwatch?->stop('colllection_element_delete');
    }

    /**
     * @param Closure $matches Should return true if the element need to be process
     * @param Closure $process The process applied to element file
     *
     * @throws FileNotFoundException
     * @throws FileExistsException
     */
    public function batchRename(string $encodedColllectionPath, Closure $matches, Closure $process): void
    {
        $this->stopwatch?->start('colllection_element_batch_rename');

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

        $this->stopwatch?->stop('colllection_element_batch_rename');
    }

    /**
     * Return file path by check and decode elementName.
     *
     * @param string $encodedElementBasename Base 64 encoded basename
     * @param string $colllectionPath        Colllection path
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
        ElementBasenameParser::getTypeByPath($basename);

        $path = $colllectionPath . '/' . $basename;

        return $path;
    }

    /**
     * Update element file with request data.
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
