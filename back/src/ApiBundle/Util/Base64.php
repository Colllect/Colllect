<?php

namespace ApiBundle\Util;

class Base64
{
    public static function isValidBase64(string $string): bool
    {
        $string = urldecode($string);

        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
            return false;
        }

        $decoded = base64_decode($string, true);

        if (!$decoded) {
            return false;
        }

        if (base64_encode($decoded) != $string) {
            return false;
        }

        return true;
    }
}
