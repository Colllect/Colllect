<?php

declare(strict_types=1);

namespace App\Util;

use App\Exception\NotSupportedElementTypeException;
use App\Model\Element\ColorsElement;
use App\Model\Element\ImageElement;
use App\Model\Element\LinkElement;
use App\Model\Element\NoteElement;

class ElementRegistry
{
    /**
     * @return string[]
     */
    public static function getExtensionsByType(): array
    {
        return [
            ColorsElement::getType() => ColorsElement::getSupportedExtensions(),
            ImageElement::getType() => ImageElement::getSupportedExtensions(),
            LinkElement::getType() => LinkElement::getSupportedExtensions(),
            NoteElement::getType() => NoteElement::getSupportedExtensions(),
        ];
    }

    /**
     * @throws NotSupportedElementTypeException
     */
    public static function getTypeForExtension(string $extension): string
    {
        foreach (self::getExtensionsByType() as $type => $extensions) {
            if (\in_array($extension, $extensions, true)) {
                return $type;
            }
        }

        throw new NotSupportedElementTypeException();
    }

    public static function isValidType(string $type): bool
    {
        $supportedTypes = array_keys(self::getExtensionsByType());
        $isSupportedType = \in_array($type, $supportedTypes, true);

        return $isSupportedType;
    }
}
