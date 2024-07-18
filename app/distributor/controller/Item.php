<?php

namespace app\distributor\controller;

use app\distributor\model;
use app\distributor\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Item extends Base
{
    public function index()
    {
        $itemAll = (new model\Item())->all();
        if (Request::isAjax()) {
            foreach ($itemAll as $key => $value) {
                $itemAll[$key] = $this->listItem($value);
            }
            return $itemAll->items() ? json_encode($itemAll->items()) : '';
        }
        View::assign(['Total' => $itemAll->total()]);
        Html::brand(Request::get('brand_id'));
        Html::wxxcx(Request::get('wxxcx_id'));
        return $this->view();
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        if ($item['brand_id']) {
            $brandOne = (new model\Brand())->one($item['brand_id']);
            $item['brand'] = $brandOne ?
                '<span style="color:' . $brandOne['color'] . ';">' . $brandOne['name'] . '</span>' : '此品牌已被删除';
        } else {
            $item['brand'] = '';
        }
        $item['price1'] = keyword($item['price1']);
        $item['price2'] = keyword($item['price2']);
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.item'), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(Config::get('page.item_wxxcx'), $item['id'], Request::get('wxxcx_id', 0));
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'item/' . $item['id'] .
            '.html?code=' . Session::get(Config::get('system.session_key_distributor') .
                '.manage_info.distributor_code');
        return $item;
    }
}
