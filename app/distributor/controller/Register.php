<?php

namespace app\distributor\controller;

use app\distributor\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;

class Register extends Base
{
    public function index()
    {
        if (!in_array(Config::get('system.register'), [2, 3])) {
            return $this->failed('本站暂未开启分销商注册！');
        }
        if (Request::isPost()) {
            $managerAdd = (new model\Manager())->add();
            if (is_numeric($managerAdd)) {
                return $managerAdd > 0 ?
                    $this->succeed(
                        Route::buildUrl('/login/index'),
                        Config::get('system.register_verify') == 0 ? '注册成功，即将跳转到登录界面！' : '注册成功，请耐心等待客服进行审核，审核通过后，方可登录！'
                    ) : $this->failed('注册失败，请重试！');
            } else {
                return $this->failed($managerAdd);
            }
        }
        return $this->view();
    }
}
