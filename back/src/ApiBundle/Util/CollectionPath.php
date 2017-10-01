<?php

namespace ApiBundle\Util;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CollectionPath
{
    const INBOX_FOLDER = 'Inbox';
    const COLLECTIONS_FOLDER = 'Collections';
    const VALID_FOLDERS = [self::INBOX_FOLDER, self::COLLECTIONS_FOLDER];

    public static function decode(string $encodedCollectionPath): string
    {
        if (!Base64::isValidBase64($encodedCollectionPath)) {
            throw new BadRequestHttpException('request.badly_encoded_collection_path');
        }

        $path = Base64::decode($encodedCollectionPath);

        if (!in_array(explode('/', $path)[0], self::VALID_FOLDERS)) {
            throw new BadRequestHttpException('request.invalid_collection_path');
        }

        return $path;
    }
}
