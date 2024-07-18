<?php

namespace wxxcx;

use CURLFile;
use think\facade\Config;

class Wxxcx
{
    public string $errMsg = 'no access';

    private string $appId;
    private string $appSecret;
    private string $token;
    private string $encodingAesKey;

    private const API_URL_PREFIX = 'https://api.weixin.qq.com/';

    public function __construct($config)
    {
        $this->appId = $config['app_id'] ?? '';
        $this->appSecret = $config['app_secret'] ?? '';
        $this->token = $config['token'] ?? '';
        $this->encodingAesKey = $config['encoding_aes_key'] ?? '';
    }

    public function getReceive($dataString = '')
    {
        if ($dataString) {
            $data = $dataCopy = $dataString;
            $decryptType = 'aes';
        } else {
            $data = $dataCopy = file_get_contents('php://input');
            $decryptType = $_GET['encrypt_type'] ?? '';
        }
        if ($decryptType == 'aes') {
            $decrypt = (new Prpcrypt($this->encodingAesKey))->decrypt(
                ((array)simplexml_load_string($dataCopy, 'SimpleXMLElement', LIBXML_NOCDATA))['Encrypt'],
                $this->appId
            );
            if (!isset($decrypt[0]) || $decrypt[0] != 0) {
                return ['decrypt error'];
            }
            $data = $decrypt[1];
        }
        $data = json_encode((array)simplexml_load_string(
            $this->unicodeDecode($data),
            'SimpleXMLElement',
            LIBXML_NOCDATA
        ));
        if (!$dataString) {
            file_put_contents(
                ROOT_DIR . '/1.txt',
                date('Y-m-d H:i:s') . '
' . $dataCopy  . $data . (is_file(ROOT_DIR . '/1.txt') ?  '

' . file_get_contents(ROOT_DIR . '/1.txt') : '')
            );
        }
        return json_decode($data, true);
    }

    public function checkSignature()
    {
        $param = [$this->token, $_GET['timestamp'] ?? '', $_GET['nonce'] ?? ''];
        sort($param, SORT_STRING);
        return sha1(implode($param)) == ($_GET['signature'] ?? '') ? ($_GET['echostr'] ?? '') : '';
    }

    public function getOauthAccessToken($code = '')
    {
        $result = $this->sendGet('sns/jscode2session?appid=' . $this->appId . '&secret=' . $this->appSecret .
            '&js_code=' . $code . '&grant_type=authorization_code');
        return $result ? json_encode($result) : false;
    }

    public function generateUrlLink($path = '', $query = '')
    {
        return $this->sendPost(
            'wxa/generate_urllink?access_token=' . $this->getAccessToken(),
            ['path' => $path, 'query' => $query]
        );
    }

    public function shopAccountGetCategoryList()
    {
        return $this->sendPost('shop/account/get_category_list?access_token=' . $this->getAccessToken(), []);
    }

    public function shopCatGet()
    {
        return $this->sendPost('shop/cat/get?access_token=' . $this->getAccessToken(), [], 'third_cat_list');
    }

    public function shopDeliveryGetCompanyList()
    {
        return $this->sendPost(
            'shop/delivery/get_company_list?access_token=' . $this->getAccessToken(),
            [],
            'company_list'
        );
    }

    public function getWxaCodeUnLimit($page = '', $scene = '')
    {
        return $this->sendPost(
            'wxa/getwxacodeunlimit?access_token=' . $this->getAccessToken(),
            ['page' => $page, 'scene' => urlencode($scene)]
        );
    }

    public function shopAuditCategory($license = [], $level = [], $certificate = [])
    {
        return $this->sendPost('shop/audit/audit_category?access_token=' . $this->getAccessToken(), [
            'audit_req' => [
                'license' => $license,
                'category_info' => [
                    'level1' => $level[0],
                    'level2' => $level[1],
                    'level3' => $level[2],
                    'certificate' => $certificate
                ]
            ]
        ], 'audit_id');
    }

    public function shopImgUpload($media)
    {
        return $this->sendPost(
            'shop/img/upload?access_token=' . $this->getAccessToken(),
            ['media' => $media, 'resp_type' => 1],
            'img_info',
            true
        );
    }

    public function shopSpuGet($outProductId)
    {
        return $this->sendPost(
            'shop/spu/get?access_token=' . $this->getAccessToken(),
            ['out_product_id' => $outProductId],
            'spu'
        );
    }

    public function shopSpuAdd($data)
    {
        return $this->sendPost('shop/spu/add?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopSpuUpdate($data)
    {
        return $this->sendPost('shop/spu/update?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopSpuDelAudit($outProductId)
    {
        return $this->sendPost(
            'shop/spu/del_audit?access_token=' . $this->getAccessToken(),
            ['out_product_id' => $outProductId]
        );
    }

    public function shopSpuListing($outProductId)
    {
        return $this->sendPost(
            'shop/spu/listing?access_token=' . $this->getAccessToken(),
            ['out_product_id' => $outProductId]
        );
    }

    public function shopSpuDelisting($outProductId)
    {
        return $this->sendPost(
            'shop/spu/delisting?access_token=' . $this->getAccessToken(),
            ['out_product_id' => $outProductId]
        );
    }

    public function shopSpuDel($outProductId)
    {
        return $this->sendPost(
            'shop/spu/del?access_token=' . $this->getAccessToken(),
            ['out_product_id' => $outProductId]
        );
    }

    public function shopOrderAdd($data)
    {
        return $this->sendPost('shop/order/add?access_token=' . $this->getAccessToken(), $data, 'data');
    }

    public function shopOrderPay($data)
    {
        return $this->sendPost('shop/order/pay?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopOrderGet($data)
    {
        return $this->sendPost('shop/order/get?access_token=' . $this->getAccessToken(), $data, 'order');
    }

    public function shopOrderGetList($page = 1, $pageSize = 100)
    {
        return $this->sendPost(
            'shop/order/get_list?access_token=' . $this->getAccessToken(),
            ['page' => $page, 'pagesize' => $pageSize, 'sort_order' => 2]
        );
    }

    public function shopDeliverySend($data)
    {
        return $this->sendPost('shop/delivery/send?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopDeliveryReceive($data)
    {
        return $this->sendPost('shop/delivery/recieve?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleAdd($data)
    {
        return $this->sendPost('shop/ecaftersale/add?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleAcceptRefund($data)
    {
        return $this->sendPost('shop/ecaftersale/acceptrefund?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleAcceptReturn($data)
    {
        return $this->sendPost('shop/ecaftersale/acceptreturn?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleReject($data)
    {
        return $this->sendPost('shop/ecaftersale/reject?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleUpdate($data)
    {
        return $this->sendPost('shop/ecaftersale/update?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleGet($data)
    {
        return $this->sendPost('shop/ecaftersale/get?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopEcAfterSaleGetList($data)
    {
        return $this->sendPost('shop/ecaftersale/get_list?access_token=' . $this->getAccessToken(), $data);
    }

    public function shopSceneCheck($scene)
    {
        return $this->sendPost(
            'shop/scene/check?access_token=' . $this->getAccessToken(),
            ['scene' => $scene],
            'is_matched'
        );
    }

    public function shopWxpayGet()
    {
        return $this->sendPost(
            'shop/wxpay/get?access_token=' . $this->getAccessToken(),
            [],
            'wxpay_req'
        );
    }

    public function shopAuditBrand($data)
    {
        return $this->sendPost(
            'shop/audit/audit_brand?access_token=' . $this->getAccessToken(),
            ['audit_req' => $data],
            'audit_id'
        );
    }

    public function shopAuditResult($auditId)
    {
        return $this->sendPost(
            'shop/audit/result?access_token=' . $this->getAccessToken(),
            ['audit_id' => $auditId],
            'data'
        );
    }

    public function shopPromoterList($page = 1, $pageSize = 20)
    {
        return $this->sendPost(
            'shop/promoter/list?access_token=' . $this->getAccessToken(),
            ['page' => $page, 'page_size' => $pageSize]
        );
    }

    public function shopOrderGetPaymentParams($data)
    {
        return $this->sendPost(
            'shop/order/getpaymentparams?access_token=' . $this->getAccessToken(),
            $data,
            'payment_params'
        );
    }

    public function expressDelivery($data)
    {
        return $this->sendPost(
            'cgi-bin/express/delivery/open_msg/trace_waybill?access_token=' . $this->getAccessToken(),
            $data,
            'waybill_token',
            false,
            true
        );
    }

    public function shopFundsGetBalance()
    {
        return $this->sendPost('shop/funds/getbalance?access_token=' . $this->getAccessToken(), '{}', 'balance_info');
    }

    public function shopAccountUpdateInfo($data)
    {
        return $this->sendPost('shop/account/update_info?access_token=' . $this->getAccessToken(), $data);
    }

    public function getUserPhoneNumber($code)
    {
        return $this->sendPost(
            'wxa/business/getuserphonenumber?access_token=' . $this->getAccessToken(),
            ['code' => $code],
            'phone_info'
        );
    }

    private function getAccessToken()
    {
        Config::load('cache/' . $this->appId, $this->appId);
        $config = Config::get($this->appId);
        if (
            isset($config['cache']) && isset($config['token']) && time() - $config['cache'] < 5400 && $config['token']
        ) {
            $accessToken = $config['token'];
        } else {
            $accessToken = $this->sendGet(
                'cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret,
                'access_token'
            );
            file_put_contents(ROOT_DIR . '/config/cache/' . $this->appId . '.php', "<?php

return [
    'cache' => " . time() . ",
    'token' => '" . $accessToken . "'
];
");
        }
        return $accessToken;
    }

    private function sendGet($urlSuffix, $returnField = '')
    {
        return $this->getData($this->httpGet(self::API_URL_PREFIX . $urlSuffix), $returnField);
    }

    private function httpGet($url)
    {
        $option = [
            CURLOPT_URL => $url,
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

    private function sendPost($urlSuffix, $data, $returnField = '', $postFile = false, $jsonHeader = false)
    {
        return $this->getData(
            $this->httpPost(
                self::API_URL_PREFIX . $urlSuffix,
                !$postFile && is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data,
                $postFile,
                $jsonHeader
            ),
            $returnField,
        );
    }

    private function httpPost($url, $data, $postFile = false, $jsonHeader = false)
    {
        $option = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true
        ];
        if (stripos($url, 'https://') !== false) {
            $option[CURLOPT_SSL_VERIFYPEER] = $option[CURLOPT_SSL_VERIFYHOST] = false;
            $option[CURLOPT_SSLVERSION] = true;
        }
        if ($jsonHeader) {
            $option[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        }
        if (is_string($data)) {
            $postFields = $data;
        } elseif ($postFile) {
            if (isset($data['media'])) {
                $filename = $data['media'];
                if (
                    version_compare(PHP_VERSION, '7.4.0', '>=') &&
                    version_compare(PHP_VERSION, '7.4.3', '<=')
                ) {
                    $data['media'] = new CURLFile($filename, '', 'new.jpg');
                    $option[CURLOPT_HTTPHEADER] =
                        ['Transfer-Encoding:', 'Content-Length: ' . (filesize($filename) + 198)];
                } else {
                    $data['media'] = new CURLFile($filename, '', substr($filename, strrpos($filename, '/') + 1));
                }
            }
            $postFields = $data;
        } else {
            $temp = [];
            foreach ($data as $key => $value) {
                $temp[] = $key . '=' . urlencode($value);
            }
            $postFields = implode('&', $temp);
        }
        $option[CURLOPT_POSTFIELDS] = $postFields;
        $curl = curl_init();
        curl_setopt_array($curl, $option);
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }

    private function getData($data, $returnField = false)
    {
        if ($data) {
            if (json_decode($data)) {
                $data = $this->escape(json_decode($data, true));
                if (!empty($data['errcode'])) {
                    $this->errMsg = $data['errmsg'];
                    return false;
                }
                if ($returnField) {
                    return $data[$returnField] ?? false;
                }
            }
            return $data;
        }
        return false;
    }

    private function escape($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = is_array($value) ? $this->escape($value) : $this->unicodeDecode($value);
            }
            return $result;
        }
        return $this->unicodeDecode($data);
    }

    private function unicodeDecode($string = '')
    {
        $result = '';
        preg_match_all('/(\\\u(\w{4}))|([\w\W ]+)/U', $string, $matches);
        foreach ($matches[0] as $value) {
            $result .= strpos($value, '\\u') === 0 ?
                iconv('UCS-2', 'UTF-8', chr(base_convert(substr($value, 2, 2), 16, 10)) .
                    chr(base_convert(substr($value, 4), 16, 10))) : $value;
        }
        return $result;
    }
}
