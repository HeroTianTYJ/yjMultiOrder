<?php

namespace app\distributor\controller;

use app\distributor\model;
use app\distributor\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Brand extends Base
{
    public function index()
    {
        $brandAll = (new model\Brand())->all();
        if (Request::isAjax()) {
            foreach ($brandAll as $key => $value) {
                $brandAll[$key] = $this->listItem($value);
            }
            return $brandAll->items() ? json_encode($brandAll->items()) : '';
        }
        View::assign(['Total' => $brandAll->total()]);
        Html::wxxcx(Request::get('wxxcx_id'));
        Html::category2(Request::get('category_id'));
        return $this->view();
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $Category = new model\Category();
        $categoryOne = $Category->one($item['category_id']);
        if ($categoryOne) {
            $categoryOne2 = $Category->one($categoryOne['parent_id']);
            $item['category'] = ($categoryOne2 ? '<span style="color:' . $categoryOne2['color'] . ';">' .
                    $categoryOne2['name'] : '父品牌分类已被删除') . ' - ' . $categoryOne['name'];
        } else {
            $item['category'] = '子品牌分类已被删除';
        }
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.brand'), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(Config::get('page.brand_wxxcx'), $item['id'], Request::get('wxxcx_id', 0));
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'brand/' . $item['id'] .
            '.html?code=' . Session::get(Config::get('system.session_key_distributor') .
                '.manage_info.distributor_code');
        return $item;
    }
}
