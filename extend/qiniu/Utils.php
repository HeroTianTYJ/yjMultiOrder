<?php

namespace qiniu;

use InvalidArgumentException;

use function json_decode;

class Utils
{
    public static function crc32Data($data)
    {
        return sprintf('%u', unpack('N', pack('H*', hash('crc32b', $data)))[1]);
    }

    public static function base64UrlSafeEncode($data)
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($data));
    }

    public static function jsonDecode($json, $assoc = false, $depth = 512)
    {
        if (empty($json)) {
            return null;
        }
        $jsonErrors = [
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        ];
        $data = json_decode($json, $assoc, $depth);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('Unable to parse JSON data: ' .
                ($jsonErrors[json_last_error()] ?? 'Unknown error'));
        }
        return $data;
    }

    public static function explodeUpToken($upToken)
    {
        $items = explode(':', $upToken);
        if (count($items) != 3) {
            return [null, null, 'invalid up token'];
        }
        return [
            $items[0],
            explode(':', self::jsonDecode(base64_decode(str_replace(['+', '/'], ['-', '_'], $items[2])))->scope)[0],
            null
        ];
    }
}
