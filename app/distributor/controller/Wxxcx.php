<?php

namespace app\distributor\controller;

use app\distributor\library\Html;
use app\distributor\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Wxxcx extends Base
{
    private array $type = ['列表页', '商品页', '品牌分类页'];

    public function index()
    {
        $wxxcxAll = (new model\Wxxcx())->all();
        if (Request::isAjax()) {
            foreach ($wxxcxAll as $key => $value) {
                $wxxcxAll[$key] = $this->listItem($value);
            }
            return $wxxcxAll->items() ? json_encode($wxxcxAll->items()) : '';
        }
        View::assign(['Total' => $wxxcxAll->total()]);
        return $this->view();
    }

    public function share()
    {
        if (Request::isAjax() && Request::post('id')) {
            $wxxcxOne = (new model\Wxxcx())->one();
            if (!$wxxcxOne) {
                return showTip('不存在此微信小程序！', 0);
            }
            if (!$wxxcxOne['app_id'] || !$wxxcxOne['app_secret']) {
                return showTip('AppID或AppSecret未设置，无法获取小程序链接和小程序码，请先联系管理员进行设置！', 0);
            }
            Html::typeRadio($this->type, 1);
            Html::product();
            Html::category2();
            Html::brand();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function getShare()
    {
        if (Request::isAjax() && Request::post('id')) {
            $wxxcxOne = (new model\Wxxcx())->one();
            if (!$wxxcxOne) {
                return showTip('不存在此微信小程序！', 0);
            }
            if (!$wxxcxOne['app_id'] || !$wxxcxOne['app_secret']) {
                return showTip('AppID或AppSecret未设置，无法获取小程序链接和小程序码，请先联系管理员进行设置！', 0);
            }
            switch (Request::post('type')) {
                case 0:
                    $pageId = $pageId2 = 0;
                    break;
                case 1:
                    $pageId = $pageId2 = 0;
                    break;
                case 2:
                    if (!(new model\Category())->one(Request::post('category_id'))) {
                        return showTip('不存在此品牌分类！', 0);
                    }
                    $pageId = $pageId2 = Request::post('category_id');
                    break;
                case 3:
                    if (!(new model\Brand())->one(Request::post('brand_id'))) {
                        return showTip('不存在此品牌！', 0);
                    }
                    $pageId = $pageId2 = Request::post('brand_id');
                    break;
                case 4:
                    $pageId = $pageId2 = 0;
                    break;
                default:
                    return showTip('获取参数有误，请重试！', 0);
            }
            $urlPrefix = 'https://wxaurl.cn/';
            $session = Session::get(Config::get('system.session_key_distributor') . '.manage_info');
            $Wxxcx = new \wxxcx\Wxxcx(['app_id' => $wxxcxOne['app_id'], 'app_secret' => $wxxcxOne['app_secret']]);
            $WxxcxShare = new model\WxxcxShare();
            $wxxcxShareOne = $WxxcxShare->one(Request::post('type'), $pageId2);
            if ($wxxcxShareOne) {
                $qrcodeId = $wxxcxShareOne['id'];
                $urlSuffix = $wxxcxShareOne['url_suffix'];
            } else {
                $generateUrlLink = $Wxxcx->generateUrlLink(
                    'pages/index/index',
                    'scene=' . Request::post('type') . '_' . $pageId . '_' . $session['distributor_code']
                );
                if (!$generateUrlLink) {
                    return showTip('小程序链接获取失败，错误代码：' . $Wxxcx->errMsg, 0);
                }
                $urlSuffix = str_replace($urlPrefix, '', $generateUrlLink['url_link']);
                if (strlen($urlSuffix) > 11) {
                    return showTip('小程序链接长度超出，请联系技术人员解决！', 0);
                }
                $qrcodeId = $WxxcxShare->add(Request::post('type'), $pageId2, $urlSuffix);
            }
            $qrcode =  'download/qrcode/' . $qrcodeId . '.jpg';
            if (!file_exists(ROOT_DIR . '/' . $qrcode)) {
                $wxaCodeUnLimit = $Wxxcx->getWxaCodeUnLimit(
                    'pages/index/index',
                    Request::post('type') . '_' . $pageId . '_' . $session['distributor_code']
                );
                if (!$wxaCodeUnLimit) {
                    return showTip('小程序码获取失败，错误代码：' . $Wxxcx->errMsg, 0);
                }
                file_put_contents(ROOT_DIR . '/' . $qrcode, $wxaCodeUnLimit);
            }
            return showTip(['qrcode' => $qrcode, 'url' => $urlPrefix . $urlSuffix]);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        return $item;
    }
}
