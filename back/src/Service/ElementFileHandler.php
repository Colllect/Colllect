<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotSupportedElementTypeException;
use App\Model\Element\ColorsElement;
use App\Model\Element\ImageElement;
use App\Model\Element\LinkElement;
use App\Model\Element\NoteElement;
use App\Model\ElementFile;
use App\Util\ElementBasenameParser;
use App\Util\ElementRegistry;
use Exception;

class ElementFileHandler
{
    private const NOT_ALLOWED_CHARS_IN_FILENAME = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];
    public const ALLOWED_IMAGE_CONTENT_TYPE = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

    /**
     * Automatic fill some elementFile fields depending on the source.
     *
     * @throws Exception
     */
    public function handleFileElement(ElementFile $elementFile): void
    {
        // If there is an UploadedFile, consider it first
        if ($elementFile->getFile()) {
            $this->handleElementFileByFile($elementFile);

            return;
        }

        // If there is an URL, check his content and try to parse URL to get basename if needed
        if ($elementFile->getUrl()) {
            $this->handleElementFileByUrl($elementFile);

            return;
        }

        // If there is directly content, use it
        if ($elementFile->getContent()) {
            return;
        }

        throw new Exception('error.empty_file');
    }

    /**
     * Use UploadedFile as source of ElementFile.
     *
     * @throws NotSupportedElementTypeException
     * @throws Exception
     */
    protected function handleElementFileByFile(ElementFile $elementFile): void
    {
        if (!$elementFile->getCleanedBasename()) {
            $elementFile->setBasename($elementFile->getFile()->getClientOriginalName());
        }

        $this->guessElementFileType($elementFile);
        $elementFile->setContent(file_get_contents($elementFile->getFile()->getRealPath()));
    }

    /**
     * Use file targeted by URL as source of ElementFile.
     *
     * @throws Exception
     */
    protected function handleElementFileByUrl(ElementFile $elementFile): void
    {
        if (!$elementFile->getCleanedBasename()) {
            $parsedUrl = parse_url($elementFile->getUrl());
            $path = explode('/', trim($parsedUrl['path'], '/'));
            $endPath = end($path);

            if (\array_key_exists('extension', pathinfo($endPath))) {
                $elementFile->setBasename($endPath);
            } else {
                $elementFile->setName($endPath);
            }
        }

        // Guess element type by header or file extension
        $this->guessElementFileType($elementFile);

        // Get URL media content needed by all types
        $mediaContent = file_get_contents($elementFile->getUrl());

        // As we know the type, adjust some attributes
        switch ($elementFile->getType()) {
            case LinkElement::getType():
                $elementFile->setContent($elementFile->getUrl());
                if (\strlen($mediaContent) > 0) {
                    $oneLinedPage = trim(preg_replace('/\s+/', ' ', $mediaContent));
                    preg_match('/\<title\>(.*)\<\/title\>/i', $oneLinedPage, $titleMatches);
                    if (isset($titleMatches[1])) {
                        $title = $titleMatches[1];
                        $title = str_replace(self::NOT_ALLOWED_CHARS_IN_FILENAME, ' ', $title);
                        $title = preg_replace('/\s+/', ' ', $title);
                        $elementFile->setBasename(trim($title));
                    }
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

        // Add default extension to typed file
        $typeExtensions = ElementRegistry::getExtensionsByType()[$elementFile->getType()];
        if (!$elementFile->getExtension() || !\in_array($elementFile->getExtension(), $typeExtensions, true)) {
            $elementFile->setExtension($typeExtensions[0]);
        }
        if (!$elementFile->getName() || \strlen($elementFile->getName()) === 0) {
            $elementFile->setName(uniqid());
        }
    }

    /**
     * Set elementFile type by trying multiple methods.
     *
     * @throws Exception
     */
    protected function guessElementFileType(ElementFile $elementFile)
    {
        if ($elementFile->getUrl()) {
            // Check if URL is valid and respond a 200
            $headers = get_headers($elementFile->getUrl(), true);
            if (strstr($headers[0], '200 OK') === false) {
                throw new Exception('error.invalid_link');
            }

            // Check if content type is in image allowed content types
            foreach (self::ALLOWED_IMAGE_CONTENT_TYPE as $allowedContentType) {
                $contentType = $headers['Content-Type'] ?? $headers['content-type'];
                if (strstr($contentType, $allowedContentType) !== false) {
                    $allowedContentTypeParts = explode('/', $allowedContentType);
                    $extension = end($allowedContentTypeParts);
                    $elementFileExtension = $elementFile->getExtension();
                    if (!isset($elementFileExtension) || (isset($elementFileExtension) && $elementFileExtension !== $extension)) {
                        $elementFile->setExtension($extension);
                    }
                    $elementFile->setType(ImageElement::getType());

                    return $elementFile;
                }
            }
        }

        try {
            $elementFile->setType(ElementBasenameParser::getTypeByPath($elementFile->getCleanedBasename()));

            return $elementFile;
        } catch (Exception $e) {
        }

        // URL works but that is not an image and extension does not allow us to guess another type
        if ($elementFile->getUrl()) {
            $elementFile->setType(LinkElement::getType());

            return $elementFile;
        }

        throw new NotSupportedElementTypeException();
    }
}
