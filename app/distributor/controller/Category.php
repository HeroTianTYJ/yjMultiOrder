<?php

namespace app\distributor\controller;

use app\distributor\library\Html;
use app\distributor\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Category extends Base
{
    public function index()
    {
        $Category = new model\Category();
        $categoryAll = $Category->all();
        if (Request::isAjax()) {
            foreach ($categoryAll as $key => $value) {
                $categoryAll[$key] = $this->listItem($value);
            }
            return $categoryAll->items() ? json_encode($categoryAll->items()) : '';
        }
        View::assign(['Total' => $categoryAll->total()]);
        Html::wxxcx(Request::get('wxxcx_id'));
        return $this->view();
    }

    private function listItem($item)
    {
        $Click = new model\Click();
        $item['name'] = keyword($item['name']);
        $clickOne = $Click->one(Config::get('page.category'), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(Config::get('page.category_wxxcx'), $item['id'], Request::get('wxxcx_id', 0));
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'category/' . $item['id'] .
            '.html?code=' . Session::get(Config::get('system.session_key_distributor') .
                '.manage_info.distributor_code');
        return $item;
    }
}
