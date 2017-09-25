<?php

namespace ApiBundle\Util;

use ApiBundle\Service\ElementFileHandler;

class Metadata
{
    public static function standardize(array $meta, string $path = null): array
    {
        // Add path if needed because some adapters didn't return it in metadata
        if ($path && !isset($meta['path'])) {
            $meta['path'] = $path;
        }

        // Set timestamp to -1 if needed because some adapters didn't return it in metadata
        if (!isset($meta['timestamp'])) {
            $meta['timestamp'] = -1;
        }

        // Add mimetype to files (ignore that for dirs)
        if ($meta['type'] !== 'dir' && !isset($meta['mimetype'])) {
            if (in_array('image/' . $meta['extension'], ElementFileHandler::ALLOWED_IMAGE_CONTENT_TYPE)) {
                $meta['mimetype'] = 'application/octet-stream';
            } else {
                $meta['mimetype'] = 'text/html; charset=UTF-8';
            }
        }

        return $meta;
    }
}
