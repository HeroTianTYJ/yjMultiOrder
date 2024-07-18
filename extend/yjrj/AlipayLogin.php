<?php

namespace yjrj;

use think\facade\Session;

class AlipayLogin
{
    private string $appId;
    private string $merchantPrivateKey;

    public function __construct($config)
    {
        $this->appId = trim($config['app_id'] ?? '');
        $this->merchantPrivateKey = trim($config['merchant_private_key'] ?? '');
    }

    public function login($redirectUri = '', $state = '')
    {
        $state = $state ?: md5(uniqid(rand(), true));
        Session::set('alipay_login_state', $state);
        header('Location:https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $this->appId .
            '&scope=auth_user&redirect_uri=' . urlencode($redirectUri) . '&state=' . $state);
    }

    public function getUserId()
    {
        return $this->getUserInfo()['user_id'] ?? '';
    }

    public function getUserInfo()
    {
        if ($_GET['state'] != Session::get('alipay_login_state')) {
            exit('state error');
        }
        Session::delete('alipay_login_state');
        $execute = $this->execute(
            'alipay.system.oauth.token',
            ['grant_type' => 'authorization_code', 'code' => $_GET['auth_code']]
        );
        $accessToken = $execute['alipay_system_oauth_token_response']['access_token'] ?? '';
        if ($accessToken) {
            $execute2 = $this->execute('alipay.user.info.share', [], $accessToken);
            if (isset($execute2['alipay_user_info_share_response'])) {
                $response = $execute2['alipay_user_info_share_response'];
                if (isset($response['code']) && $response['code'] == 10000) {
                    unset($response['code'], $response['msg']);
                    return $response;
                }
            }
        }
        return [];
    }

    private function execute($method = '', $param = [], $authToken = '')
    {
        $systemParam = [
            'alipay_sdk' => 'alipay-sdk-PHP-4.19.282.ALL',
            'app_id' => $this->appId,
            'charset' => 'UTF-8',
            'format' => 'json',
            'method' => $method,
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0'
        ];
        if ($authToken) {
            $systemParam['auth_token'] = $authToken;
        }
        $systemParam['sign'] = $this->generateSign(array_merge($param, $systemParam));

        $requestUrl = 'https://openapi.alipay.com/gateway.do?';
        foreach ($systemParam as $key => $value) {
            if ($value) {
                $requestUrl .= $key . '=' . urlencode($value) . '&';
            }
        }
        return json_decode(iconv('UTF-8', 'UTF-8//IGNORE', $this->httpPost(substr($requestUrl, 0, -1), $param)), true);
    }

    private function generateSign($param)
    {
        $param = array_filter($param);
        $param['sign_type'] = 'RSA2';
        openssl_sign($this->getSignContent($param), $sign, "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->merchantPrivateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----", OPENSSL_ALGO_SHA256);
        return base64_encode($sign);
    }

    private function getSignContent($param)
    {
        ksort($param);
        $stringToBeSigned = '';
        $i = 0;
        foreach ($param as $key => $value) {
            if ('@' != substr($value, 0, 1)) {
                if ($i == 0) {
                    $stringToBeSigned .= $key . '=' . $value;
                } else {
                    $stringToBeSigned .= '&' . $key . '=' . $value;
                }
                $i++;
            }
        }
        return $stringToBeSigned;
    }

    private function httpPost($url, $data = [])
    {
        $postStr = '';
        foreach ($data as $key => $value) {
            $postStr .= $key . '=' . urlencode($value) . '&';
        }
        $option = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => substr($postStr, 0, -1),
            CURLOPT_RETURNTRANSFER => true
        ];
        if (stripos($url, 'https://') !== false) {
            $option[CURLOPT_SSL_VERIFYPEER] = $option[CURLOPT_SSL_VERIFYHOST] = false;
            $option[CURLOPT_SSLVERSION] = true;
        }
        $curl = curl_init();
        curl_setopt_array($curl, $option);
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }
}
