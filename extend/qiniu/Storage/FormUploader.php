<?php

namespace qiniu\Storage;

use qiniu\Config;
use qiniu\Http\Client;
use qiniu\Utils;

class FormUploader
{
    public static function put($upToken, $key, $data, $filename)
    {
        $fields['token'] = $upToken;
        if ($key !== null) {
            $fields['key'] = $key;
        }
        $fields['crc32'] = Utils::crc32Data($data);

        [$accessKey, $bucket, $err] = Utils::explodeUpToken($upToken);
        if ($err != null) {
            return [null, $err];
        }

        $multipartPost = Client::multipartPost(
            (new Config())->getUpHost($accessKey, $bucket),
            $fields,
            $filename,
            $data
        );
        if (!$multipartPost->ok()) {
            return [];
        }
        return [$multipartPost->json(), null];
    }
}
