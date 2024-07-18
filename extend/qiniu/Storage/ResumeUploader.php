<?php

namespace qiniu\Storage;

use Exception;
use qiniu\Config;
use qiniu\Http\Client;
use qiniu\Utils;

class ResumeUploader
{
    private string $upToken;
    private string $filename;
    private $inputStream;
    private int $size;
    private array $contexts = [];
    private string $host;

    /**
     * ResumeUploader constructor.
     * @param $upToken
     * @param $filename
     * @param $inputStream
     * @param $size
     * @throws Exception
     */
    public function __construct($upToken, $filename, $inputStream, $size)
    {
        [$accessKey, $bucket, $err] = Utils::explodeUpToken($upToken);
        if ($err != null) {
            throw new Exception($err->message(), 1);
        }

        $this->upToken = $upToken;
        $this->filename = $filename;
        $this->inputStream = $inputStream;
        $this->size = $size;
        $this->host = (new Config())->getUpHost($accessKey, $bucket);
    }

    /**
     * @param $filename
     * @return array
     * @throws Exception
     */
    public function upload($filename)
    {
        $uploaded = 0;
        while ($uploaded < $this->size) {
            $blockSize = $this->size < $uploaded + Config::BLOCK_SIZE ? $this->size - $uploaded : Config::BLOCK_SIZE;
            $data = fread($this->inputStream, $blockSize);
            if ($data === false) {
                throw new Exception('file read failed', 1);
            }
            $crc = Utils::crc32Data($data);
            $response = $this->makeBlock($data, $blockSize);
            $ret = null;
            if ($response->ok() && $response->json() != null) {
                $ret = $response->json();
            }
            if ($response->needRetry() || !isset($ret['crc32']) || $crc != $ret['crc32']) {
                $response = $this->makeBlock($data, $blockSize);
                $ret = $response->json();
            }

            if (!$response->ok() || !isset($ret['crc32']) || $crc != $ret['crc32']) {
                return [];
            }
            $this->contexts[] = $ret['ctx'];
            $uploaded += $blockSize;
        }
        $url = $this->host . '/mkfile/' . $this->size . '/mimeType/' .
            Utils::base64UrlSafeEncode('application/octet-stream');
        if ($this->filename != null) {
            $url .= '/key/' . Utils::base64UrlSafeEncode($this->filename);
        }
        $url .= '/fname/' . Utils::base64UrlSafeEncode($filename);
        $response = $this->post($url, implode(',', $this->contexts));
        return $response->ok() ? [$response->json(), null] : [];
    }

    private function makeBlock($block, $blockSize)
    {
        return $this->post($this->host . '/mkblk/' . $blockSize, $block);
    }

    private function post($url, $body)
    {
        return Client::post($url, $body, ['Authorization' => 'UpToken ' . $this->upToken]);
    }
}
