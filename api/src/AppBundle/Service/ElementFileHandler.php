<?php

namespace AppBundle\Service;

use AppBundle\Exception\NotSupportedElementTypeException;
use AppBundle\Model\Element;
use AppBundle\Model\ElementFile;

class ElementFileHandler
{
    const ALLOWED_IMAGE_CONTENT_TYPE = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    const NOT_ALLOWED_CHARS_IN_FILENAME = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];


    /**
     * Automatic fill some elementFile fields depending on the source
     *
     * @param ElementFile $elementFile
     * @return bool
     * @throws \Exception
     */
    public function handleFileElement(ElementFile $elementFile)
    {
        // If there is an UploadedFile, consider it first
        if ($elementFile->getFile()) {
            $this->handleElementFileByFile($elementFile);

            return true;
        }

        // If there is an URL, check his content and try to parse URL to get basename if needed
        if ($elementFile->getUrl()) {
            $this->handleElementFileByUrl($elementFile);

            return true;
        }

        // If there is directly content, use it
        if ($elementFile->getContent()) {
            return true;
        }

        throw new \Exception('error.empty_file');
    }

    /**
     * Use UploadedFile as source of ElementFile
     *
     * @param ElementFile $elementFile
     */
    protected function handleElementFileByFile(ElementFile $elementFile)
    {
        if (!$elementFile->getBasename()) {
            $elementFile->setBasename($elementFile->getFile()->getClientOriginalName());
        }

        $this->guessElementFileType($elementFile);
        $elementFile->setContent(file_get_contents($elementFile->getFile()->getRealPath()));
    }

    /**
     * Use file targeted by URL as source of ElementFile
     *
     * @param ElementFile $elementFile
     * @throws \Exception
     */
    protected function handleElementFileByUrl(ElementFile $elementFile)
    {
        if (!$elementFile->getBasename()) {
            $parsedUrl = parse_url($elementFile->getUrl());
            $path = explode('/', $parsedUrl['path']);
            $elementFile->setBasename(end($path));
        }

        // Guess element type by header or file extension
        $this->guessElementFileType($elementFile);

        // Get URL media content needed by all types
        $mediaContent = file_get_contents($elementFile->getUrl());

        // As we know the type, adjust some attributes
        switch ($elementFile->getType()) {
            case Element::LINK_TYPE:
                $elementFile->setContent($elementFile->getUrl());
                if (strlen($mediaContent) > 0) {
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
            case Element::IMAGE_TYPE:
            case Element::NOTE_TYPE:
            case Element::COLORS_TYPE:
                $elementFile->setContent($mediaContent);
                break;
            default:
                throw new NotSupportedElementTypeException();
        }

        // Add default extension to typed file
        $pathInfos = pathinfo($elementFile->getBasename());
        if (!isset($pathInfos['extension']) || !in_array($pathInfos['extension'], Element::EXTENSIONS_BY_TYPE[$elementFile->getType()])) {
            $elementFile->setBasename($elementFile->getBasename() . '.' . Element::EXTENSIONS_BY_TYPE[$elementFile->getType()][0]);
        }
        if (!isset($pathInfos['filename']) || strlen($pathInfos['filename']) === 0) {
            $elementFile->setBasename(uniqid() . $elementFile->getBasename());
        }
    }

    /**
     * Set elementFile type by trying multiple methods
     *
     * @param ElementFile $elementFile
     * @return ElementFile
     * @throws \Exception
     */
    protected function guessElementFileType(ElementFile $elementFile)
    {
        if ($elementFile->getUrl()) {
            // Check if URL is valid and respond a 200
            $headers = get_headers($elementFile->getUrl(), true);
            if (strstr($headers[0], '200 OK') === false) {
                throw new \Exception('error.invalid_link');
            }

            // Check if content type is in image allowed content types
            foreach (self::ALLOWED_IMAGE_CONTENT_TYPE as $allowedContentType) {
                if (strstr($headers['Content-Type'], $allowedContentType) !== false) {
                    $allowedContentTypeParts = explode('/', $allowedContentType);
                    $extension = end($allowedContentTypeParts);
                    $pathInfos = pathinfo($elementFile->getBasename());
                    if (!isset($pathInfos['extension']) || (isset($pathInfos['extension']) && $pathInfos['extension'] !== $extension)) {
                        $elementFile->setBasename($elementFile->getBasename() . '.' . $extension);
                    }
                    $elementFile->setType(Element::IMAGE_TYPE);
                    return $elementFile;
                }
            }
        }

        try {
            $elementFile->setType(Element::getTypeByPath($elementFile->getBasename()));
            return $elementFile;
        } catch (\Exception $e) {
        }

        // URL works but that is not an image and extension does not allow us to guess another type
        if ($elementFile->getUrl()) {
            $elementFile->setType(Element::LINK_TYPE);
            return $elementFile;
        }

        throw new NotSupportedElementTypeException();
    }
}
