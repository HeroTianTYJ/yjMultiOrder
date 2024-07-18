<?php

namespace qiniu\Http;

use qiniu\Config;

class Client
{
    public static function get($url)
    {
        return self::sendRequest('GET', $url);
    }

    public static function post($url, $body, array $headers = [])
    {
        return self::sendRequest('POST', $url, $headers, $body);
    }

    public static function multipartPost($url, $fields, $fileName, $fileBody)
    {
        $data = [];
        $mimeBoundary = md5(microtime());
        foreach ($fields as $key => $value) {
            $data[] = '--' . $mimeBoundary;
            $data[] = 'Content-Disposition:form-data;name="' . $key . '"';
            $data[] = '';
            $data[] = $value;
        }
        $data[] = '--' . $mimeBoundary;
        $data[] = 'Content-Disposition:form-data;name="file";filename="' .
            str_replace(['\\', '"'], ['\\\\', '\\\"'], $fileName) . '"';
        $data[] = 'Content-Type:application/octet-stream';
        $data[] = '';
        $data[] = $fileBody;
        $data[] = '--' . $mimeBoundary . '--';
        $data[] = '';
        return self::sendRequest(
            'POST',
            $url,
            ['Content-Type' => 'multipart/form-data;boundary=' . $mimeBoundary],
            implode("\r\n", $data)
        );
    }

    private static function sendRequest($method, $url, $headers = [], $body = [])
    {
        $curl = curl_init();
        $options = [
            CURLOPT_USERAGENT => 'QiniuPHP/' . Config::SDK_VER . '(' . php_uname('s') . '/' . php_uname('m') . ')' .
                'PHP/' . phpversion(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_URL => $url
        ];
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        if (!empty($headers)) {
            $headersResult = [];
            foreach ($headers as $key => $value) {
                $headersResult[] = $key . ': ' . $value;
            }
            $options[CURLOPT_HTTPHEADER] = $headersResult;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Expect:']);
        if ($body) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $ret = curl_errno($curl);
        if ($ret !== 0) {
            $r = new Response(-1, [], null, curl_error($curl));
            curl_close($curl);
            return $r;
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl);

        $headers = [];
        foreach (explode("\r\n", substr($result, 0, $headerSize)) as $line) {
            $temp = explode(':', trim($line));
            if (count($temp) > 1) {
                $temp[0] = str_replace('- ', '-', ucwords(str_replace('-', '- ', $temp[0])));
                $headers[$temp[0]] = trim($temp[1]);
            }
        }

        return new Response($code, $headers, substr($result, $headerSize));
    }
}
