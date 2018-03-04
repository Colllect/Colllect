<?php

namespace ApiBundle\EnhancedFlysystemAdapter;

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

        $this->client->move($prefixedPath, $prefixedNewPath);

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
                $this->fillCleanedPathDisplay($cleanedPathDisplay, $item['path_display']);
            }
        }

        if (count($cleanedPathDisplay) === 0) {
            $this->fillCleanedPathDisplay($cleanedPathDisplay, $directory);
        }

        $cleanedPathDisplay = array_reverse($cleanedPathDisplay);

        $result = array_map(function ($entry) use ($cleanedPathDisplay) {
            $path = $this->removePathPrefix($entry['path_display']);

            foreach ($cleanedPathDisplay as $pathLower => $pathDisplay) {
                $path = preg_replace('/^\/?' . preg_quote($pathLower, '/') . '/i', $pathDisplay, $path);
            }

            $entry['path_display'] = $path;

            return $this->normalizeResponse($entry);
        }, $result['entries']);

        return $result;
    }

    private function fillCleanedPathDisplay(array &$cleanedPathDisplay, string $pathDisplay)
    {
        $pathLower = strtolower($pathDisplay);
        $cleanedPathDisplay[$pathLower] = $pathDisplay;

        $pathParts = explode('/', $pathLower);
        while (count($pathParts) > 3) {
            array_pop($pathParts);
            $parentPathLower = implode('/', $pathParts);
            if (!array_key_exists($parentPathLower, $cleanedPathDisplay)) {
                $prefixedPath = $this->applyPathPrefix($parentPathLower);
                $metadata = $this->client->getMetadata($prefixedPath);
                $cleanedPathDisplay = array_merge([
                    $metadata['path_lower'] => $metadata['path_display']
                ], $cleanedPathDisplay);
            }
        }
    }
}
