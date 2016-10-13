<?php

namespace AppBundle\Util;

use AppBundle\Model\Element;
use AppBundle\Model\ElementFile;

class ElementUtil
{
    const ALLOWED_IMAGE_CONTENT_TYPE = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

    /**
     * @param ElementFile $elementFile
     * @return bool
     * @internal param string $path
     */
    public static function isValidElement(ElementFile $elementFile)
    {
        try {
            self::guessElementFileType($elementFile);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param ElementFile $elementFile
     * @return string
     * @throws \Exception
     * @internal param string $path
     */
    public static function guessElementFileType(ElementFile $elementFile)
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
                    return Element::IMAGE_TYPE;
                }
            }
        }

        $pathInfos = pathinfo($elementFile->getBasename());
        if (isset($pathInfos['extension'])) {
            foreach (Element::EXTENSIONS_BY_TYPE as $type => $extensions) {
                if (in_array($pathInfos['extension'], $extensions)) {
                    return $type;
                }
            }
        }

        // URL works but that is not an image and extension does not allow us to guess another type
        if ($elementFile->getUrl()) {
            return Element::LINK_TYPE;
        }

        throw new \Exception('error.unsupported_element_type');
    }
}