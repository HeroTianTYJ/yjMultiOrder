<?php

namespace qiniu\Storage;

use qiniu\Auth;
use qiniu\Config;
use qiniu\Http\Client;

use qiniu\Utils;
use function qiniu\base64UrlSafeEncode;

class BucketManager
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function delete($bucket, $key)
    {
        $url = 'http://' . Config::RS_HOST . '/delete/' . Utils::base64UrlSafeEncode($bucket . ':' . $key);
        $post = Client::post($url, [], ['Authorization' => 'QBox ' . $this->auth->signRequest($url)]);
        if (!$post->ok()) {
            return [];
        }
        return [$post->body === null ? [] : $post->json(), null];
    }
}
