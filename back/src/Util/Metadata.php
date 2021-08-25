<?php

declare(strict_types=1);

namespace App\Util;

use App\Service\ElementFileHandler;

class Metadata
{
    /**
     * @param array<string, string|int|null> $meta
     *
     * @return array<string, string|int>
     */
    public static function standardize(array $meta, ?string $path = null): array
    {
        // Add path if needed because some adapters didn't return it in metadata
        if ($path && !isset($meta['path'])) {
            $meta['path'] = $path;
        }

        // Add filename as it is not returned sometimes
        if (isset($meta['path']) && !isset($meta['filename'])) {
            $pathParts = explode('/', (string) $meta['path']);
            $meta['basename'] = end($pathParts);

            if (!str_contains($meta['basename'], '.')) {
                $meta['filename'] = $meta['basename'];
            } else {
                $basenameParts = explode('.', $meta['basename']);
                $extension = array_pop($basenameParts);

                if (!empty($extension)) {
                    $meta['extension'] = strtolower($extension);
                }

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

        return [
            'path' => (string) $meta['path'],
            'type' => (string) $meta['type'],
            'size' => $meta['size'] ?? 0,
            'mimetype' => $meta['mimetype'] ?? '',
            'timestamp' => $meta['timestamp'] ?? 0,
            'basename' => (string) $meta['basename'],
            'extension' => $meta['extension'] ?? '',
            'filename' => (string) $meta['filename'],
        ];
    }
}
