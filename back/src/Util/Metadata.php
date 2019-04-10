<?php

declare(strict_types=1);

namespace App\Util;

use App\Service\ElementFileHandler;

class Metadata
{
    public static function standardize(array $meta, string $path = null): array
    {
        // Add path if needed because some adapters didn't return it in metadata
        if ($path && !isset($meta['path'])) {
            $meta['path'] = $path;
        }

        // Add filename as it is not returned sometimes
        if (isset($meta['path']) && !isset($meta['filename'])) {
            $pathParts = explode('/', $path);
            $meta['basename'] = end($pathParts);
            if (strpos('.', $meta['basename']) === false) {
                $meta['filename'] = $meta['basename'];
            } else {
                $basenameParts = explode('.', $meta['basename']);
                $meta['extension'] = array_pop($basenameParts);
                $meta['filename'] = implode('.', $basenameParts);
            }
        }

        // Add mimetype to files (ignore that for dirs)
        if ($meta['type'] !== 'dir' && !isset($meta['mimetype'])) {
            if (isset($meta['extension']) && \in_array(
                    'image/' . $meta['extension'],
                    ElementFileHandler::ALLOWED_IMAGE_CONTENT_TYPE,
                    true
                )) {
                $meta['mimetype'] = 'image/' . $meta['extension'];
            } else {
                $meta['mimetype'] = 'text/html; charset=UTF-8';
            }
        }

        return $meta;
    }
}
