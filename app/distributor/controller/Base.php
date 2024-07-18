<?php

namespace app\distributor\controller;

use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\facade\Session;
use think\facade\View;

class Base extends \app\common\controller\Base
{
    //初始化
    protected function initialize()
    {
        parent::initialize();

        $this->checkLogin();
        $this->publicAssign();
    }

    //模板引入方法重写
    protected function view($template = '', $code = 200)
    {
        $run = '';
        if (Config::get('app.demo')) {
            $run .= '<script type="text/javascript" src="static/index/js/visit.js?' . staticCache() . '"></script>
<script type="text/javascript" src="https://hm.baidu.com/hm.js?dab4e321ea03f29004923ce2db634a44" async></script>';
        }
        View::assign(['Run' => $run]);
        return parent::view($template, $code);
    }

    //公共变量
    private function publicAssign()
    {
        $data = [];
        if (Session::has(Config::get('system.session_key_distributor') . '.manage_info')) {
            $data['Navigator'] = $this->navigator();
        }
        View::assign($data);
    }

    //登录验证
    private function checkLogin()
    {
        $manageInfo = Config::get('system.session_key_distributor') . '.manage_info';
        if (Session::has($manageInfo)) {
            $session = Session::get($manageInfo);
            if (
                isset($session['id'], $session['name'], $session['distributor_code']) && is_numeric($session['id']) &&
                $session['name'] && $session['distributor_code']
            ) {
                if (Request::controller() == 'Login' && Request::action() != 'logout') {
                    $this->succeed(Route::buildUrl('/index/index'));
                }
            } elseif (Request::controller() != 'Login') {
                $this->error('登录信息不合法，请重新登录！', 5, Route::buildUrl('/login/index'));
            }
        } else {
            if (Request::controller() == 'Index') {
                $this->succeed(Route::buildUrl('/login/index'));
            } elseif (!in_array(Request::controller(), ['Login', 'Register'])) {
                $this->error('非法登录！', 5, Route::buildUrl('/login/index'));
            }
        }
    }

    //后台导航
    private function navigator()
    {
        $navigator = [
            'index' => [
                'one' => '首页',
                'two' => []
            ],
            'share' => [
                'one' => '推广',
                'two' => [
                    'Item/index' => '商品页',
                    'Lists/index' => '列表页',
                    'Brand/index' => '品牌',
                    'Category/index' => '品牌分类',
                    'Wxxcx/index' => '微信小程序'
                ]
            ],
            'order' => [
                'one' => '订单',
                'two' => [
                    'Order/index' => '订单',
                    'OrderStatistic/index' => '订单统计'
                ]
            ],
            'distributor' => [
                'one' => '销商',
                'two' => [
                    'Balance/index' => '结算记录',
                    'Withdraw/index' => '提现记录'
                ]
            ],
            'data' => [
                'one' => '数据',
                'two' => [
                    'Visit/index' => '访问统计',
                    'VisitWxxcx/index' => '访问统计（微信小程序）'
                ]
            ],
            'profile' => [
                'one' => '',
                'two' => [
                    'Profile/index' => ['个人资料'],
                    'Profile/loginRecord' => ['登录记录']
                ]
            ]
        ];
        foreach ($navigator as $key => $value) {
            $navigator[$key]['active'] = $value['two'] ?
                (in_array(Request::controller() . '/' . Request::action(), keyToArray($value['two'])) ||
                    in_array(Request::controller() . '/index', keyToArray($value['two']))) :
                parse_name(Request::controller()) == $key;
        }
        return $navigator;
    }
}
