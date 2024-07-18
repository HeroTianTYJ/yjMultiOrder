<?php

namespace app\distributor\controller;

use app\distributor\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use yjrj\QqLogin;
use yjrj\QQWry;
use yjrj\Wechat;

class Profile extends Base
{
    public function index()
    {
        $Manager = new model\Manager();
        if (Request::isAjax()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，个人资料无法修改！', 0);
            }
            $managerModify = $Manager->modify();
            return is_numeric($managerModify) ? showTip('个人资料修改成功！') : showTip($managerModify, 0);
        }
        View::assign(['One' => $Manager->one()]);
        return $this->view();
    }

    public function loginRecord()
    {
        $loginRecordManagerAll = (new model\LoginRecordManager())->all();
        if (Request::isAjax()) {
            foreach ($loginRecordManagerAll as $key => $value) {
                $loginRecordManagerAll[$key] = $this->listItem($value);
            }
            return $loginRecordManagerAll->items() ?
                json_encode($loginRecordManagerAll->items()) : '';
        }
        View::assign(['Total' => $loginRecordManagerAll->total()]);
        return $this->view();
    }

    public function wechat()
    {
        if (in_array(device(), ['androidWechat', 'iphoneWechat', 'windowsWechat', 'macWechat'])) {
            $Wechat = new Wechat([
                'app_id' => Config::get('system.wechat_app_id'),
                'app_secret' => Config::get('system.wechat_app_secret'),
                'bridge' => Config::get('system.wechat_bridge_domain') ? Config::get('system.wechat_bridge_domain') .
                    Config::get('system.index_php') . 'bridge/wechat.html' : ''
            ]);
        } else {
            $Wechat = new Wechat([
                'app_id' => Config::get('system.wechat_open_app_id'),
                'app_secret' => Config::get('system.wechat_open_app_secret'),
                'bridge' => Config::get('system.wechat_open_bridge_domain') ?
                    Config::get('system.wechat_open_bridge_domain') . Config::get('system.index_php') .
                    'bridge/wechat.html' : '',
                'is_mp' => false
            ]);
        }
        $Wechat->oauthRedirect(Config::get('url.web1') . 'callback.php/' . app('http')->getName() . '/wechatBind.html');
    }

    public function qq()
    {
        (new QqLogin([
            'app_id' => Config::get('system.qq_app_id'),
            'app_key' => Config::get('system.qq_app_key'),
            'redirect_uri' => Config::get('url.web1') . 'callback.php/' . app('http')->getName() . '/qqBind.html',
            'bridge' => Config::get('system.qq_bridge_domain') ? Config::get('system.qq_bridge_domain') .
                Config::get('system.index_php') . 'bridge/qq.html' : ''
        ]))->login();
    }

    private function listItem($item)
    {
        $item['ip'] = keyword($item['ip']) . '<br>' . QQWry::getAddress($item['ip']);
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
