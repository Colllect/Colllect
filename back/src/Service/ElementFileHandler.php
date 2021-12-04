<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EmptyFileException;
use App\Exception\InvalidElementLinkException;
use App\Exception\NotSupportedElementTypeException;
use App\Model\Element\ColorsElement;
use App\Model\Element\ImageElement;
use App\Model\Element\LinkElement;
use App\Model\Element\NoteElement;
use App\Model\ElementFile;
use App\Util\ElementBasenameParser;
use App\Util\ElementRegistry;
use Exception;
use LogicException;

class ElementFileHandler
{
    private const NOT_ALLOWED_CHARS_IN_FILENAME = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];
    public const ALLOWED_IMAGE_CONTENT_TYPE = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

    /**
     * Automatic fill some elementFile fields depending on the source.
     *
     * @throws NotSupportedElementTypeException
     * @throws InvalidElementLinkException
     * @throws EmptyFileException
     */
    public function handleFileElement(ElementFile $elementFile): void
    {
        // If there is an UploadedFile, consider it first
        if ($elementFile->getFile() !== null) {
            $this->handleElementFileByFile($elementFile);

            return;
        }

        // If there is an URL, check his content and try to parse URL to get basename if needed
        if ($elementFile->getUrl() !== null) {
            $this->handleElementFileByUrl($elementFile);

            return;
        }

        // If there is directly content, use it
        if ($elementFile->getContent() !== null) {
            return;
        }

        throw new EmptyFileException();
    }

    /**
     * Use UploadedFile as source of ElementFile.
     *
     * @throws NotSupportedElementTypeException
     * @throws InvalidElementLinkException
     * @throws Exception
     */
    protected function handleElementFileByFile(ElementFile $elementFile): void
    {
        $file = $elementFile->getFile();

        if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            throw new Exception('No file to handle');
        }

        if ($elementFile->getCleanedBasename() === null) {
            $clientOriginalName = $file->getClientOriginalName();

            if ($clientOriginalName !== null) {
                $elementFile->setBasename($clientOriginalName);
            }
        }

        $this->guessElementFileType($elementFile);

        $fileRealPath = $file->getRealPath();

        if ($fileRealPath === false) {
            throw new Exception('File does not exists: ' . $fileRealPath);
        }

        $fileContent = file_get_contents($fileRealPath);

        if ($fileContent === false) {
            throw new Exception('Cannot read file: ' . $fileRealPath);
        }

        $elementFile->setContent($fileContent);
    }

    /**
     * Set elementFile type by trying multiple methods.
     *
     * @throws NotSupportedElementTypeException
     * @throws InvalidElementLinkException
     */
    protected function guessElementFileType(ElementFile $elementFile): ElementFile
    {
        $elementFileUrl = $elementFile->getUrl();

        if ($elementFileUrl !== null) {
            // Check if URL is valid and respond a 200
            $headers = get_headers($elementFileUrl);
            if ($headers === false || strstr($headers[0], '200 OK') === false) {
                throw new InvalidElementLinkException('error.invalid_link');
            }

            // Check if content type is in image allowed content types
            foreach (self::ALLOWED_IMAGE_CONTENT_TYPE as $allowedContentType) {
                $contentType = $headers['Content-Type'] ?? $headers['content-type'];
                if (strstr($contentType, $allowedContentType) !== false) {
                    $allowedContentTypeParts = explode('/', $allowedContentType);
                    /** @var string|false $extension */
                    $extension = end($allowedContentTypeParts);
                    if ($extension === false) {
                        throw new LogicException('Content type must contain a slash');
                    }
                    $elementFile->setExtension($extension);
                    $elementFile->setType(ImageElement::getType());

                    return $elementFile;
                }
            }
        }

        try {
            $elementCleanedBasename = $elementFile->getCleanedBasename();

            if ($elementCleanedBasename !== null) {
                $elementFile->setType(ElementBasenameParser::getTypeByPath($elementCleanedBasename));

                return $elementFile;
            }
        } catch (Exception) {
        }

        // URL works but that is not an image and extension does not allow us to guess another type
        if ($elementFileUrl !== null) {
            $elementFile->setType(LinkElement::getType());

            return $elementFile;
        }

        throw new NotSupportedElementTypeException();
    }

    /**
     * Use file targeted by URL as source of ElementFile.
     *
     * @throws InvalidElementLinkException
     * @throws LogicException
     * @throws NotSupportedElementTypeException
     */
    protected function handleElementFileByUrl(ElementFile $elementFile): void
    {
        $elementUrl = $elementFile->getUrl();

        if ($elementUrl === null) {
            throw new LogicException('Element must have an URL');
        }

        if ($elementFile->getCleanedBasename() === null) {
            $path = explode('/', trim($elementUrl, '/'));
            /** @var string|false $endPath */
            $endPath = end($path);

            if ($endPath === false) {
                throw new LogicException('URL must contain a slash');
            }

            if (\array_key_exists('extension', pathinfo($endPath))) {
                $elementFile->setBasename($endPath);
            } else {
                $elementFile->setName($endPath);
            }
        }

        // Guess element type by header or file extension
        $this->guessElementFileType($elementFile);

        // Get URL media content needed by all types
        $mediaContent = file_get_contents($elementUrl);

        // As we know the type, adjust some attributes based on content
        if ($mediaContent !== false && $mediaContent !== '') {
            switch ($elementFile->getType()) {
                case LinkElement::getType():
                    $elementFile->setContent($elementUrl);
                    $oneLinedPage = preg_replace('/\s+/', ' ', $mediaContent);
                    if ($oneLinedPage === null) {
                        break;
                    }
                    preg_match('/<title>(.*)<\/title>/i', trim($oneLinedPage), $titleMatches);
                    if (isset($titleMatches[1])) {
                        $title = $titleMatches[1];
                        $title = str_replace(self::NOT_ALLOWED_CHARS_IN_FILENAME, ' ', $title);
                        $title = preg_replace('/\s+/', ' ', $title);
                        if (!\is_string($title)) {
                            break;
                        }
                        $elementFile->setBasename(trim($title));
                    }
                    break;
                case ImageElement::getType():
                case NoteElement::getType():
                case ColorsElement::getType():
                    $elementFile->setContent($mediaContent);
                    break;
                default:
                    throw new NotSupportedElementTypeException();
            }
        }

        // Add default extension to typed file
        $typeExtensions = ElementRegistry::getExtensionsByType()[$elementFile->getType()];
        if (!$elementFile->getExtension() || !\in_array($elementFile->getExtension(), $typeExtensions, true)) {
            $elementFile->setExtension($typeExtensions[0]);
        }
        if ($elementFile->getName() === null || $elementFile->getName() === '') {
            $elementFile->setName(uniqid());
        }
    }
}
