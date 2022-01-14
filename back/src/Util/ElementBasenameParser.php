<?php

declare(strict_types=1);

namespace App\Util;

use App\Exception\NotSupportedElementTypeException;

class ElementBasenameParser
{
    /**
     * Parse basename.
     *
     * @throws NotSupportedElementTypeException
     */
    public static function getTypeByPath(string $elementFilePath): string
    {
        $pathInfos = pathinfo($elementFilePath);

        if (!isset($pathInfos['extension'])) {
            throw new NotSupportedElementTypeException();
        }

        return ElementRegistry::getTypeForExtension($pathInfos['extension']);
    }

    /**
     * Parse basename to get type, name, tags and extension.
     *
     * @return array<string, mixed>
     *
     * @throws NotSupportedElementTypeException
     */
    public static function parse(string $basename): array
    {
        $meta = [];

        // Can throw an NotSupportedElementTypeException
        $meta['type'] = self::getTypeByPath($basename);

        $pathParts = pathinfo($basename);
        $filename = $pathParts['filename'];

        // Parse tags from filename
        preg_match_all('#\#([^\s.,\/\#!$%^&*;:{}=\-`~()]+)#', $filename, $tags);
        $tags = $tags[1];
        foreach ($tags as $k => $tag) {
            // Remove tags from filename
            $filename = str_replace(sprintf('#%s', $tag), '', $filename);
            // Replace underscores by spaces in tags
            $tags[$k] = str_replace('_', ' ', $tag);
        }

        sort($tags);

        // Replace multiple spaces by single space
        $name = preg_replace('#\s+#', ' ', trim($filename));

        $meta['name'] = $name;
        $meta['tags'] = $tags;

        if (isset($pathParts['extension'])) {
            $meta['extension'] = $pathParts['extension'];
        }

        return $meta;
    }
}
