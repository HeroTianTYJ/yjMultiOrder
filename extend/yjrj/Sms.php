<?php

namespace yjrj;

class Sms
{
    private string $accessKeyId;
    private string $accessKeySecret;
    private string $user;
    private string $pass;

    public function __construct($config = [])
    {
        $this->accessKeyId = $config['accessKeyId'] ?? '';
        $this->accessKeySecret = $config['accessKeySecret'] ?? '';
        $this->user = $config['user'] ?? '';
        $this->pass = $config['pass'] ?? '';
    }

    public function smsBao($mobile, $content)
    {
        $error = [30 => '短信宝密码错误', 40 => '短信宝账号不存在', 41 => '短信宝余额不足', 42 => '短信宝账户已过期',
            43 => 'IP地址限制', 50 => '内容含有敏感词'];
        $result = file_get_contents('https://api.smsbao.com/sms?u=' . $this->user . '&p=' . md5($this->pass) . '&m=' .
            $mobile . '&c=' . urlencode($content));
        return $result == 0 ? 1 : $error[$result] ?? '';
    }

    public function aly($param)
    {
        $param['TemplateParam'] = json_encode($param['TemplateParam']);
        $param = array_merge([
            'AccessKeyId' => $this->accessKeyId,
            'Action' => 'SendSms',
            'Format' => 'json',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => md5(uniqid() . uniqid(md5(microtime(true)), true)),
            'SignatureVersion' => '1.0',
            'Timestamp' => gmdate('Y-m-d\\TH:i:s\\Z'),
            'Version' => '2017-05-25'
        ], $param);
        $param['Signature'] = $this->getRPCSignature($param);
        return json_decode(file_get_contents('https://dysmsapi.aliyuncs.com/?' . http_build_query($param)), true);
    }

    private function getRPCSignature($param)
    {
        ksort($param);
        $paramArr = [];
        foreach ($param as $key => $value) {
            if ($value) {
                $paramArr[] = rawurlencode($key) . '=' . rawurlencode($value);
            }
        }
        return base64_encode(hash_hmac('sha1', 'GET&' . rawurlencode('/') . '&' .
            rawurlencode(implode('&', $paramArr)), $this->accessKeySecret . '&', true));
    }
}
