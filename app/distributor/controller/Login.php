<?php

namespace app\distributor\controller;

use app\distributor\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\facade\Session;
use yjrj\QqLogin;
use yjrj\Wechat;

class Login extends Base
{
    public function index()
    {
        if (Request::isPost()) {
            $managerLogin = (new model\Manager())->login();
            if (is_object($managerLogin)) {
                if (passEncode(Request::post('pass')) != $managerLogin['pass']) {
                    return showTip('账号或密码不正确！', 0);
                }
                $loginDo = $this->loginDo($managerLogin);
                if ($loginDo != '1') {
                    return $loginDo;
                }
                return showTip('登录成功，跳转中。。。');
            } elseif (is_string($managerLogin)) {
                return showTip($managerLogin, 0);
            } else {
                return showTip('账号或密码不正确！', 0);
            }
        }
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
        $Wechat->oauthRedirect(Config::get('url.web1') . 'callback.php/' . app('http')->getName() .
            '/wechatLogin.html');
    }

    public function qq()
    {
        (new QqLogin([
            'app_id' => Config::get('system.qq_app_id'),
            'app_key' => Config::get('system.qq_app_key'),
            'redirect_uri' => Config::get('url.web1') . 'callback.php/' . app('http')->getName() . '/qqLogin.html',
            'bridge' => Config::get('system.qq_bridge_domain') ? Config::get('system.qq_bridge_domain') .
                Config::get('system.index_php') . 'bridge/qq.html' : ''
        ]))->login();
    }

    public function logout()
    {
        Session::delete(Config::get('system.session_key_distributor'));
        return $this->succeed(Route::buildUrl('/' . parse_name(Request::controller()) . '/index'), '您已退出登录！', 1);
    }

    protected function loginDo($managerLogin)
    {
        if ($managerLogin['is_activation'] == 0) {
            return Request::isAjax() ?
                showTip('您的账号尚未激活，无法登录，请联系超级管理员进行激活！', 0) :
                $this->failed('您的账号尚未激活，无法登录，请联系超级管理员进行激活！', 0);
        }
        if ($managerLogin['level'] != 3) {
            return Request::isAjax() ?
                showTip('您不是分销商用户，请到主后台进行登录！', 0) :
                $this->failed('您不是分销商用户，请到主后台进行登录！', 0);
        }

        $sessionKeyDistributor = Config::get('system.session_key_distributor');
        $loginInfo = Session::get($sessionKeyDistributor . '.login_info');
        if ($loginInfo) {
            if ($loginInfo['type'] == 'wechat') {
                if ($managerLogin['wechat_open_id'] || $managerLogin['wechat_union_id']) {
                    return showTip('您的账号已绑定其它微信号，无法再绑定此微信号！', 0);
                }
                (new model\Manager())->wechatOpenId($loginInfo['openid'], $loginInfo['unionid'], $managerLogin['id']);
            } elseif ($loginInfo['type'] == 'qq') {
                if ($managerLogin['qq_open_id']) {
                    return showTip('您的账号已绑定其它QQ号，无法再绑定此QQ号！', 0);
                }
                (new model\Manager())->qqOpenId($loginInfo['openid'], $managerLogin['id']);
            }
        }

        (new model\LoginRecordManager())->add($managerLogin['id']);
        Session::set($sessionKeyDistributor . '.manage_info', [
            'id' => $managerLogin['id'],
            'name' => $managerLogin['name'],
            'distributor_code' => $managerLogin['distributor_code']
        ]);
        return '1';
    }
}
