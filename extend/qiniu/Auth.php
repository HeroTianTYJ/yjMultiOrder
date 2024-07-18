<?php

namespace qiniu;

class Auth
{
    private string $accessKey;
    private string $secretKey;

    public function __construct($accessKey, $secretKey)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    public function signRequest($urlString)
    {
        $url = parse_url($urlString);
        $data = '';
        if (array_key_exists('path', $url)) {
            $data = $url['path'];
        }
        if (array_key_exists('query', $url)) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";
        return $this->sign($data);
    }

    public function uploadToken($bucket)
    {
        $encodedData = Utils::base64UrlSafeEncode(json_encode(['scope' => $bucket, 'deadline' => time() + 3600]));
        return $this->sign($encodedData) . ':' . $encodedData;
    }

    private function sign($data)
    {
        return $this->accessKey . ':' . Utils::base64UrlSafeEncode(hash_hmac('sha1', $data, $this->secretKey, true));
    }
}
