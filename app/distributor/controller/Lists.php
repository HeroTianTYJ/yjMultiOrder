<?php

namespace app\distributor\controller;

use app\distributor\model;
use app\distributor\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Lists extends Base
{
    public function index()
    {
        $listsAll = (new model\Lists())->all();
        if (Request::isAjax()) {
            foreach ($listsAll as $key => $value) {
                $listsAll[$key] = $this->listItem($value);
            }
            return $listsAll->items() ? json_encode($listsAll->items()) : '';
        }
        View::assign(['Total' => $listsAll->total()]);
        Html::wxxcx(Request::get('wxxcx_id'));
        return $this->view();
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.lists'), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(Config::get('page.lists_wxxcx'), $item['id'], Request::get('wxxcx_id', 0));
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') .
            ($item['module'] ?: 'id/' . $item['id']) . '.html?code=' .
            Session::get(Config::get('system.session_key_distributor') . '.manage_info.distributor_code');
        return $item;
    }
}
