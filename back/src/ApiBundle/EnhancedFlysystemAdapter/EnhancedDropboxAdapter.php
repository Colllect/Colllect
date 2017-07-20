<?php

namespace ApiBundle\EnhancedFlysystemAdapter;

use Spatie\Dropbox\Exceptions\BadRequest;
use Spatie\FlysystemDropbox\DropboxAdapter;

class EnhancedDropboxAdapter extends DropboxAdapter implements EnhancedFlysystemAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function renameDir(string $path, string $newPath): bool
    {
        $prefixedPath = $this->applyPathPrefix($path);
        $prefixedNewPath = $this->applyPathPrefix($newPath);

        try {
            $this->client->move($prefixedPath, $prefixedNewPath);
        } catch (BadRequest $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false): array
    {
        $location = $this->applyPathPrefix($directory);

        $result = $this->client->listFolder($location, $recursive);

        if (!count($result['entries'])) {
            return [];
        }

        $cleanedPathDisplay = [];
        foreach ($result['entries'] as $item) {
            if ($item['.tag'] === 'folder') {
                $cleanedPathDisplay[$item['path_lower']] = $item['path_display'];

                $pathParts = explode('/', $item['path_lower']);
                do {
                    array_pop($pathParts);
                    $parentPathLower = implode('/', $pathParts);
                    if (!array_key_exists($parentPathLower, $cleanedPathDisplay)) {
                        $prefixedPath = $this->applyPathPrefix($parentPathLower);
                        $metadata = $this->client->getMetadata($prefixedPath);
                        $cleanedPathDisplay = [$metadata['path_lower'] => $metadata['path_display']] + $cleanedPathDisplay;
                    }
                } while (count($pathParts) > 2);
            }
        }

        $cleanedPathDisplay = array_reverse($cleanedPathDisplay);

        $result = array_map(function ($entry) use ($cleanedPathDisplay) {
            $path = $this->removePathPrefix($entry['path_display']);

            foreach ($cleanedPathDisplay as $pathLower => $pathDisplay) {
                $path = preg_replace('/^' . preg_quote($pathLower, '/') . '/i', $pathDisplay, $path);
            }

            $entry['path_display'] = $path;

            return $this->normalizeResponse($entry);
        }, $result['entries']);

        return $result;
    }
}
