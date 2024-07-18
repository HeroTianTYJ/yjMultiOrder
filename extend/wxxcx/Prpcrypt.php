<?php

namespace wxxcx;

use Exception;

class Prpcrypt
{
    private $key;
    private const OK = 0;
    private const VALIDATE_APP_ID_ERROR = 40005;
    private const ENCRYPT_AES_ERROR = 40006;
    private const DECRYPT_AES_ERROR = 40007;
    private const ILLEGAL_BUFFER = 40008;

    public function __construct($k)
    {
        $this->key = base64_decode($k . '=');
    }

    public function encrypt($text, $appId)
    {
        try {
            return [self::OK, openssl_encrypt((new Pkcs7())->encode(
                $this->getRandomStr() . pack('N', strlen($text)) . $text . $appId
            ), 'AES-256-CBC', substr($this->key, 0, 32), OPENSSL_ZERO_PADDING, substr($this->key, 0, 16))];
        } catch (Exception $e) {
            return [self::ENCRYPT_AES_ERROR, null];
        }
    }

    public function decrypt($encrypted, $appId)
    {
        try {
            $decrypted = openssl_decrypt(
                $encrypted,
                'AES-256-CBC',
                substr($this->key, 0, 32),
                OPENSSL_ZERO_PADDING,
                substr($this->key, 0, 16)
            );
        } catch (Exception $e) {
            return [self::DECRYPT_AES_ERROR, null];
        }
        try {
            $decode = (new Pkcs7())->decode($decrypted);
            if (strlen($decode) < 16) {
                return '';
            }
            $content = substr($decode, 16, strlen($decode));
            $xmlLen = unpack('N', substr($content, 0, 4))[1];
            $fromAppId = substr($content, $xmlLen + 4);
            if (!$appId) {
                $appId = $fromAppId;
            }
            return $fromAppId != $appId ?
                [self::VALIDATE_APP_ID_ERROR, null] :
                [0, substr($content, 4, $xmlLen), $fromAppId];
        } catch (Exception $e) {
            return [self::ILLEGAL_BUFFER, null];
        }
    }

    private function getRandomStr()
    {
        $key = '';
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 16; $i++) {
            $key .= $charset[mt_rand(0, strlen($charset) - 1)];
        }
        return $key;
    }
}
