<?php

namespace app\index\controller;

use app\index\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use yjrj\Page;

class Base extends \app\common\controller\Base
{
    //初始化
    protected function initialize()
    {
        parent::initialize();
        $this->distributor();
    }

    //数据分页
    protected function page($total, $pageSize = 0, $url = '', $parameter = [])
    {
        $Page = new Page($total, $pageSize ?: Config::get('app.page_size'), $url, $parameter);
        View::assign(['Page' => $Page->show()]);
        return $Page->firstRow;
    }

    //分销商信息记录
    private function distributor()
    {
        $sessionKeyIndex = Config::get('system.session_key_index');
        if (Request::get('code') && !Session::has($sessionKeyIndex . '.distributor_id')) {
            $managerOne = (new model\Manager())->one2();
            if ($managerOne && $managerOne['level'] == 3) {
                Session::set($sessionKeyIndex . '.distributor_id', $managerOne['id']);
                Session::set($sessionKeyIndex . '.distributor_code', Request::get('code'));
            }
        }
    }
}
