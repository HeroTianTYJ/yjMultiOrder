<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use ZipArchive;

class Wxxcx extends Base
{
    private array $type = ['列表页', '商品页', '品牌分类页'];

    public function index()
    {
        return $this->failed('完善中，敬请期待。。。', 0);
        $wxxcxAll = (new model\Wxxcx())->all();
        if (Request::isAjax()) {
            foreach ($wxxcxAll as $key => $value) {
                $wxxcxAll[$key] = $this->listItem($value);
            }
            return $wxxcxAll->items() ? json_encode($wxxcxAll->items()) : '';
        }
        View::assign(['Total' => $wxxcxAll->total()]);
        Html::typeSelect($this->type, Request::get('type', -1));
        Html::lists(Request::get('lists_id'));
        Html::item(Request::get('item_id'));
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $wxxcxAdd = (new model\Wxxcx())->add();
                if (is_numeric($wxxcxAdd)) {
                    return $wxxcxAdd > 0 ? showTip('微信小程序添加成功！') : showTip('微信小程序添加失败！', 0);
                } else {
                    return showTip($wxxcxAdd, 0);
                }
            }
            Html::typeRadio($this->type);
            Html::lists();
            Html::item();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo') && Request::post('id') == 1) {
                return showTip('演示站，id为1的微信小程序无法修改！', 0);
            }
            $Wxxcx = new model\Wxxcx();
            $wxxcxOne = $Wxxcx->one();
            if (!$wxxcxOne) {
                return showTip('不存在此微信小程序！', 0);
            }
            if (Request::get('action') == 'do') {
                $wxxcxModify = $Wxxcx->modify($wxxcxOne['text_id_pay_cert_private_key']);
                return is_numeric($wxxcxModify) ?
                    showTip(['msg' => '微信小程序修改成功！', 'data' => $this->listItem($Wxxcx->one())]) :
                    showTip($wxxcxModify, 0);
            }
            Html::typeRadio($this->type, $wxxcxOne['type']);
            Html::lists($wxxcxOne['type'] == 0 ? $wxxcxOne['page_id'] : 0);
            Html::item($wxxcxOne['type'] == 1 ? $wxxcxOne['page_id'] : 0);
            $wxxcxOne['pay_cert_private_key'] = (new model\Text())->content($wxxcxOne['text_id_pay_cert_private_key']);
            View::assign(['One' => $wxxcxOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，微信小程序无法删除！', 0);
            }
            $Wxxcx = new model\Wxxcx();
            $file = $textId = [];
            if (Request::post('id')) {
                $wxxcxOne = $Wxxcx->one();
                if (!$wxxcxOne) {
                    return showTip('不存在此微信小程序！', 0);
                }
                $file[] = ROOT_DIR . '/download/' . $wxxcxOne['zip'] . '.zip';
                $textId[] = $wxxcxOne['text_id_pay_cert_private_key'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $wxxcxOne = $Wxxcx->one($value);
                    if (!$wxxcxOne) {
                        return showTip('不存在您勾选的微信小程序！', 0);
                    }
                    $file[] = ROOT_DIR . '/download/' . $wxxcxOne['zip'] . '.zip';
                    $textId[] = $wxxcxOne['text_id_pay_cert_private_key'];
                }
            }
            if ($Wxxcx->remove()) {
                foreach ($file as $value) {
                    if (is_file($value)) {
                        unlink($value);
                    }
                }

                (new model\Click())->remove2();
                (new model\Text())->remove($textId);

                $WxxcxShare = new model\WxxcxShare();
                foreach ($WxxcxShare->all2() as $value) {
                    $qrcode = ROOT_DIR . '/download/qrcode/' . $value['id'] . '.jpg';
                    if (is_file($qrcode)) {
                        unlink($qrcode);
                    }
                }
                $WxxcxShare->remove2();

                return showTip('微信小程序删除成功！');
            } else {
                return showTip('微信小程序删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function zip()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (!isset($_SERVER['HTTPS'])) {
                return showTip('您当前没有使用https方式访问后台，无法打包小程序。如果您的网站已开启https，请<a href="https:' .
                    Config::get('url.web2') . Config::get('system.manager_enter') . '" target="_blank">点击此处</a>' .
                    '访问后台进行打包小程序。如果访问失败，有可能是还没开启https，请先联系客服开启！', 0);
            }
            if (Config::get('app.demo')) {
                return showTip('演示站，无法打包微信小程序！', 0);
            }
            $Wxxcx = new model\Wxxcx();
            $wxxcxOne = $Wxxcx->one();
            if (!$wxxcxOne) {
                return showTip('不存在此微信小程序！', 0);
            }
            $filename = passEncode(Request::post('id'));
            $ZipArchive = new ZipArchive();
            if (
                $ZipArchive->open(
                    ROOT_DIR . '/download/' . $filename . '.zip',
                    ZipArchive::CREATE | ZipArchive::OVERWRITE | ZipArchive::CM_STORE
                )
            ) {
                if ($wxxcxOne['type'] == 0) {
                    $listsOne = (new model\Lists())->one($wxxcxOne['page_id']);
                    if (!$listsOne) {
                        return showTip('此列表页已被删除，无法打包微信小程序！', 0);
                    }
                    $ZipArchive->addFromString('pages/index/index.js', $this->indexJs($wxxcxOne));
                    $ZipArchive->addFromString('pages/index/index.wxml', $this->indexWxml());
                    $ZipArchive->addFromString('pages/item/item.js', $this->itemJs($wxxcxOne));
                    $ZipArchive->addFromString('pages/item/item.wxml', $this->itemWxml());
                    $ZipArchive->addFromString('app.json', $this->appJson($listsOne['name'], $wxxcxOne['type']));
                } elseif ($wxxcxOne['type'] == 1) {
                    $itemOne = (new model\Item())->one($wxxcxOne['page_id']);
                    if (!$itemOne) {
                        return showTip('此商品页已被删除，无法打包微信小程序！', 0);
                    }
                    $ZipArchive->addFromString('pages/index/index.js', $this->itemJs($wxxcxOne, 1));
                    $ZipArchive->addFromString('pages/index/index.wxml', $this->itemWxml());
                    $ZipArchive->addFromString('app.json', $this->appJson($itemOne['name'], $wxxcxOne['type']));
                } elseif ($wxxcxOne['type'] == 2) {
                    $ZipArchive->addFromString('pages/index/index.js', $this->categoryJs());
                    $ZipArchive->addFromString('pages/index/index.wxml', $this->categoryWxml());
                    $ZipArchive->addFromString('pages/brand/brand.js', $this->brandJs());
                    $ZipArchive->addFromString('pages/brand/brand.wxml', $this->brandWxml());
                    $ZipArchive->addFromString('pages/item/item.js', $this->itemJs($wxxcxOne));
                    $ZipArchive->addFromString('pages/item/item.wxml', $this->itemWxml());
                    $ZipArchive->addFromString('app.json', $this->appJson($wxxcxOne['name'], $wxxcxOne['type']));
                }
                $ZipArchive->addFromString('pages/connect/connect.js', 'Page({})');
                $ZipArchive->addFromString('pages/connect/connect.wxss', $this->connectWxss());
                $ZipArchive->addFromString('pages/connect/connect.wxml', $this->connectWxml());
                $ZipArchive->addFromString('pages/pay/pay.js', $this->payJs());
                $ZipArchive->addFromString('pages/pay/pay.wxml', ' ');
                $ZipArchive->addFromString('app.js', $this->appJs($wxxcxOne['submit_key']));
                $ZipArchive->addFromString('app.wxss', ' ');
                $ZipArchive->close();

                $Wxxcx->modify2($filename);
                return showTip('微信小程序打包成功！');
            } else {
                return showTip('微信小程序打包失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function download()
    {
        if (Request::get('id')) {
            $wxxcxOne = (new model\Wxxcx())->one(Request::get('id'));
            if (!$wxxcxOne) {
                return $this->failed('不存在此微信小程序！');
            }
            $filename = $wxxcxOne['zip'] . '.zip';
            $file = ROOT_DIR . '/download/' . $filename;
            if (!is_file($file)) {
                return $this->failed('不存在此压缩包！');
            }
            downloadFileToLocal(file_get_contents($file), $filename);
            return '';
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function deleteZip()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Wxxcx = new model\Wxxcx();
            $wxxcxOne = $Wxxcx->one();
            if (!$wxxcxOne) {
                return showTip('不存在此微信小程序！', 0);
            }
            $file = ROOT_DIR . '/download/' . $wxxcxOne['zip'] . '.zip';
            if (is_file($file)) {
                unlink($file);
            }
            $Wxxcx->modify2();
            return showTip('压缩包删除成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    /*public function qrcode()
    {
        if (Request::get('id')) {
            $session = Session::get(Config::get('system.session_key'));
            if ($session['level'] != 3) {
                return $this->failed('您不是分销商，不能通过此方式获取小程序码！');
            }
            $Wxxcx = new model\Wxxcx();
            $wxxcxOne = $Wxxcx->one();
            if (!$wxxcxOne) {
                return $this->failed('不存在此微信小程序！');
            }
            if (!$wxxcxOne['app_id'] || !$wxxcxOne['app_secret']) {
                return $this->failed('AppID或AppSecret未设置，请先联系管理员进行设置！');
            }
            $page = $scene = $picPath = '';
            if ($wxxcxOne['type'] != 2) {
                if (Request::get('page_id', -1) != -1) {
                    if (Request::get('page_id') == 0) {
                        $page = 'pages/index/index';
                        $scene = $session['id'];
                        $picPath = 'download/qrcode/0-' . Request::get('id') . '-0-' . $session['id'] . '.jpg';
                    } else {
                        $page = 'pages/item/item';
                        $scene = Request::get('page_id') . '_' . $session['id'];
                        $picPath = 'download/qrcode/1-' . Request::get('id') . '-' . Request::get('page_id') . '-' .
                            $session['id'] . '.jpg';
                    }
                }
                if ($wxxcxOne['type'] == 0) {
                    $this->item2($wxxcxOne['page_id'], Request::get('page_id'));
                }
            } else {
                $this->category(Request::get('category_id'));
                $this->item(Request::get('item_id'));
                $brandOne = (new model\Brand())->one2(Request::get('brand_id'));
                $brandName = $brandOne ? $brandOne['name'] : '';
                View::assign(['Brand' => $brandName]);
                if (Request::get('type2', -1) != -1) {
                    if (Request::get('type2') == 0) {
                        $page = 'pages/index/index';
                        $scene = Request::get('category_id') . '_' . $session['id'];
                        $picPath = 'download/qrcode/2-' . Request::get('id') . '-' . Request::get('category_id') . '-' .
                            $session['id'] . '.jpg';
                    } elseif (Request::get('type2') == 1) {
                        if (Request::get('brand_id', 0) == 0) {
                            return $this->failed('请先选择一个品牌！');
                        }
                        $page = 'pages/brand/brand';
                        $scene = Request::get('brand_id') . '_' . $session['id'];
                        $picPath = 'download/qrcode/3-' . Request::get('id') . '-' . Request::get('brand_id') . '-' .
                            $session['id'] . '.jpg';
                    } elseif (Request::get('type2') == 2) {
                        $page = 'pages/item/item';
                        $scene = Request::get('item_id') . '_' . $session['id'];
                        $picPath = 'download/qrcode/1-' . Request::get('id') . '-' . Request::get('item_id') . '-' .
                            $session['id'] . '.jpg';
                    }
                }
            }
            if ($page && $picPath) {
                if (!file_exists(ROOT_PATH . '/' . $picPath)) {
                    $token = $this->getToken($wxxcxOne);
                    if ($token == '1') {
                        return $this->failed('token值超出157位，请联系技术人员解决！');
                    }
                    file_put_contents(
                        ROOT_PATH . '/' . $picPath,
                        curlPost(
                            'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $token,
                            json_encode(['page' => $page, 'scene' => urlencode($scene)])
                        )
                    );
                }
            }
            $wxxcxOne['pic_path'] = $picPath;
            View::assign(['One' => $wxxcxOne]);
            return $this->view();
        } else {
            return $this->failed('非法操作！');
        }
    }

    public function test()
    {
        print_r(curlPost('https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' .
            $this->getToken((new model\Wxxcx())->one()), json_encode([
            'touser' => 'ouPf00ChHi8dYDtCZ3pq8PzaQxI8',
            'template_id' => '3r6PeT9xvlx9N1j4FoZ54DTuxVrUGfpPrGRikRY33cQ',
            'form_id' => '1',
            'data' => [
                'keyword1' => ['value' => '123'],
                'keyword2' => ['value' => '10']
            ]
        ])));
    }

    private function getToken($wxxcxOne)
    {
        if ($wxxcxOne['token'] && time() - $wxxcxOne['token_time'] < 5400) {
            return $wxxcxOne['token'];
        } else {
            $token = json_decode(curlGet(
                'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $wxxcxOne['app_id'] .
                '&secret=' . $wxxcxOne['app_secret']
            ))->access_token;
            if (strlen($token) > 157) {
                return '1';
            }
            (new model\Wxxcx())->modify3($token);
            return $token;
        }
    }*/

    private function appJs($submitKey)
    {
        return "App({data:{web_url:'" . Config::get('app.web_url') . "api.php/',key:'" . $submitKey . "',wxxcx_id:" .
            Request::get('id') . "},onShow:function(options){this.data.scene_id=options.scene;}})";
    }

    private function appJson($name, $type)
    {
        return '{"pages":["pages/index/index"' . ($type != 1 ? ',"pages/item/item"' : '') .
            ($type == 2 ? ',"pages/brand/brand"' : '') . ',"pages/pay/pay","pages/connect/connect"],"window":{' .
            '"backgroundTextStyle":"light","navigationBarBackgroundColor":"#fff","navigationBarTitleText":"' . $name .
            '","navigationBarTextStyle":"black"}}';
    }

    private function connectWxss()
    {
        return '.button{background:#07C160;padding:8px;font-weight:700;font-size:17px;color:#FFF;' .
            'line-height:1.41176471;border-radius:4px;margin:180px 0;}.button-hover{background:#06AD56;}';
    }

    private function connectWxml()
    {
        return '<button open-type="contact" class="button" hover-class="button-hover">进入客服会话</button>';
    }

    private function indexJs($wxxcxOne)
    {
        return 'Page({data:{id:' . $wxxcxOne['page_id'] . '},onLoad:function(options){let that=this,' .
            "scene=decodeURIComponent(options.scene);that.setData({manager_id:scene!='undefined'?scene:0,web_url:" .
            "getApp().data.web_url});wx.request({url:getApp().data.web_url+'click',method:'POST',data:{id:" .
            "that.data.id,manager_id:that.data.manager_id,type:3,scene_id:getApp().data.scene_id,key:" .
            "getApp().data.key,wxxcx_id:getApp().data.wxxcx_id},header:{'content-type':" .
            "'application/x-www-form-urlencoded'},success:function(res){if(res.data=='-1'){wx.showModal({title:'提示'," .
            "content:'小程序key有误，无法和系统保持正常通信，请重新上传小程序！',showCancel:false,success:function(){wx.navigateBack({delta:1});" .
            "}});return;}that.data.title=res.data.title;}});},onShareAppMessage:function(){return{title:" .
            "this.data.title,desc:'',path:'/pages/index/index?scene='+this.data.manager_id}}})";
    }

    private function indexWxml()
    {
        return '<web-view src="{{web_url}}id/{{id}}.html?manager_id={{manager_id}}"></web-view>';
    }

    private function itemJs($wxxcxOne, $index = 0)
    {
        return "Page({onLoad:function(options){let that=this,scene=decodeURIComponent(options.scene).split('_');" .
            "that.setData({id:" . ($index ? $wxxcxOne['page_id'] : 'scene[0]') . ",manager_id:scene[1]!=undefined?" .
            "scene[1]:0,web_url:getApp().data.web_url});wx.request({url:getApp().data.web_url+'click',method:'POST'," .
            "data:{id:that.data.id,manager_id:that.data.manager_id,type:1,scene_id:scene[2]!=undefined?scene[2]:" .
            "getApp().data.scene_id,key:getApp().data.key,wxxcx_id:getApp().data.wxxcx_id},header:{'content-type':" .
            "'application/x-www-form-urlencoded'},success:function(res){if(res.data=='-1'){wx.showModal({title:'提示'," .
            "content:'小程序key有误，无法和系统保持正常通信，请重新上传小程序！',showCancel:false,success:function(){wx.navigateBack({delta:1});" .
            "}});return;}that.data.title=res.data.title;}});},onShareAppMessage:function(){return{title:" .
            "this.data.title,desc:'',path:'/pages/" . ($index ? 'index/index' : 'item/item') .
            "?scene='+this.data.id+'_'+this.data.manager_id}}})";
    }

    private function itemWxml()
    {
        return '<web-view src="{{web_url}}item/{{id}}.html?manager_id={{manager_id}}"></web-view>';
    }

    private function categoryJs()
    {
        return "Page({data:{id:0},onLoad:function(options){let scene=decodeURIComponent(options.scene).split('_');" .
            "wx.request({url:getApp().data.web_url+'click/verify',method:'POST',data:{key:getApp().data.key,wxxcx_id:" .
            "getApp().data.wxxcx_id},header:{'content-type':'application/x-www-form-urlencoded'},success:" .
            "function(res){if(res.data=='-1'){wx.showModal({title:'提示',content:'小程序key有误，无法和系统保持正常通信，请重新上传小程序！'," .
            "showCancel:false,success:function(){wx.navigateBack({delta:1});}});return;}}});this.setData({id:" .
            "scene[0]!='undefined'?scene[0]:0,manager_id:scene[1]!=undefined?scene[1]:0,scene_id:scene[2]!=undefined?" .
            "scene[2]:getApp().data.scene_id,web_url:getApp().data.web_url,wxxcx_id:getApp().data.wxxcx_id});},a:" .
            "function(e){let data=e.detail.data;this.setData({id:data[data.length-1].id});this.data.title=" .
            "data[data.length-1].title;},onShareAppMessage:function(options){return{title:this.data.title,desc:''," .
            "path:'/pages/index/index?scene='+this.data.id+'_'+this.data.manager_id};}})";
    }

    private function categoryWxml()
    {
        return '<web-view src="{{web_url}}category/{{id}}.html?manager_id={{manager_id}}&scene_id={{scene_id}}&' .
            'wxxcx_id={{wxxcx_id}}" bindmessage="a"></web-view>';
    }

    private function brandJs()
    {
        return "Page({data:{id:0},onLoad:function(options){let scene=decodeURIComponent(options.scene).split('_');" .
            "wx.request({url:getApp().data.web_url+'click/verify',method:'POST',data:{key:getApp().data.key,wxxcx_id:" .
            "getApp().data.wxxcx_id},header:{'content-type':'application/x-www-form-urlencoded'},success:" .
            "function(res){if(res.data=='-1'){wx.showModal({title:'提示',content:'小程序key有误，无法和系统保持正常通信，请重新上传小程序！'," .
            "showCancel:false,success:function(){wx.navigateBack({delta:1});}});return;}}});this.setData({id:" .
            "scene[0]!='undefined'?scene[0]:0,manager_id:scene[1]!=undefined?scene[1]:0,scene_id:" .
            "scene[2]!=undefined?scene[2]:getApp().data.scene_id,web_url:getApp().data.web_url,wxxcx_id:" .
            "getApp().data.wxxcx_id});},a:function(e){let data=e.detail.data;this.setData({id:data[data.length-1].id}" .
            ");this.data.title=data[data.length-1].title;},onShareAppMessage:function(options){return{title:" .
            "this.data.title,desc:'',path:'/pages/brand/brand?scene='+this.data.id+'_'+this.data.manager_id};}})";
    }

    private function brandWxml()
    {
        return '<web-view src="{{web_url}}brand/{{id}}.html?manager_id={{manager_id}}&scene_id={{scene_id}}&' .
            'wxxcx_id={{wxxcx_id}}" bindmessage="a"></web-view>';
    }

    private function payJs()
    {
        return 'Page({onLoad:function(options){let that=this;wx.login({success:function(res){if(res.code){wx.request' .
            "({url:getApp().data.web_url+'login',method:'POST',data:{code:res.code,key:getApp().data.key,wxxcx_id:" .
            "getApp().data.wxxcx_id},header:{'content-type':'application/x-www-form-urlencoded'},success:function" .
            "(res2){if(res2.data=='-1'){wx.showModal({title:'提示',content:'小程序key有误，无法和系统保持正常通信，请重新上传小程序！',showCancel" .
            ":false,success:function(){wx.navigateBack({delta:1});}});return;}wx.request({url:getApp().data.web_url+" .
            "'pay/wxpay?order_id='+options.order_id,method:'POST',data:{openid:res2.data.openid,wxxcx_id:getApp()." .
            "data.wxxcx_id},success:function(res3){wx.requestPayment({timeStamp:res3.data.timeStamp,nonceStr:res3." .
            "data.nonceStr,package:'prepay_id='+res3.data.prepay_id,signType:'MD5',paySign:res3.data.paySign,success" .
            ":function(res4){wx.showModal({title:'提示',content:res3.data.tip,showCancel:false,success:function(){" .
            "wx.navigateBack({delta:1});}});}});},header:{'content-type':'application/x-www-form-urlencoded'}});}});}" .
            '}});}})';
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        if ($item['type'] == 0) {
            $listsOne = (new model\Lists())->one($item['page_id']);
            $item['page'] = $listsOne ? $listsOne['name'] : '此列表页已被删除';
        } elseif ($item['type'] == 1) {
            $itemOne = (new model\Item())->one2($item['page_id']);
            $item['page'] = $itemOne ? $itemOne['name'] : '此商品页已被删除';
        } elseif ($item['type'] == 2) {
            $item['page'] = '-';
        }
        $item['type'] = $this->type[$item['type']];
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
