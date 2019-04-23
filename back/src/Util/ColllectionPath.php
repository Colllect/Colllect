<?php

declare(strict_types=1);

namespace App\Util;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ColllectionPath
{
    public const COLLLECTIONS_FOLDER = 'Colllections';
    private const INBOX_FOLDER = 'Inbox';
    private const VALID_FOLDERS = [self::INBOX_FOLDER, self::COLLLECTIONS_FOLDER];

    public static function decode(string $encodedColllectionPath): string
    {
        if (!Base64::isValidBase64($encodedColllectionPath)) {
            throw new BadRequestHttpException('request.badly_encoded_colllection_path');
        }

        $path = Base64::decode($encodedColllectionPath);

        if (!\in_array(explode('/', $path)[0], self::VALID_FOLDERS, true)) {
            throw new BadRequestHttpException('request.invalid_colllection_path');
        }

        return $path;
    }
}
