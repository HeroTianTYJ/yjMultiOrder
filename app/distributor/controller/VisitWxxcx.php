<?php

namespace app\distributor\controller;

use app\distributor\model;
use app\distributor\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use yjrj\QQWry;

class VisitWxxcx extends Base
{
    private array $type = ['列表页', '商品页', '品牌分类页', '品牌详情页'];

    public function index()
    {
        $visitWxxcxAll = (new model\VisitWxxcx())->all();
        if (Request::isAjax()) {
            foreach ($visitWxxcxAll as $key => $value) {
                $visitWxxcxAll[$key] = $this->listItem($value);
            }
            return $visitWxxcxAll->items() ? json_encode($visitWxxcxAll->items()) : '';
        }
        View::assign(['Total' => $visitWxxcxAll->total()]);
        Html::wxxcx(Request::get('wxxcx_id'));
        Html::typeSelect($this->type, Request::get('type', -1));
        if (Request::get('type', -1) == 0) {
            Html::lists(Request::get('lists_id'));
        } elseif (Request::get('type') == 1) {
            Html::item(Request::get('item_id'));
        } elseif (Request::get('type') == 2) {
            Html::category1(Request::get('category_id'));
        } elseif (Request::get('type') == 3) {
            Html::brand(Request::get('brand_id'));
        }
        Html::wxxcxScene(Request::get('wxxcx_scene_id'));
        return $this->view();
    }

    private function listItem($item)
    {
        $item['ip'] = keyword($item['ip']) . '<br>' . QQWry::getAddress($item['ip']);
        $wxxcxOne = (new model\Wxxcx())->one($item['wxxcx_id']);
        $item['wxxcx'] = $wxxcxOne ? $wxxcxOne['name'] : '此微信小程序已被删除';
        if ($item['type'] == 0) {
            $listsOne = (new model\Lists())->one($item['page_id']);
            $item['page'] = $listsOne ? $listsOne['name'] : '此列表页已被删除';
        } elseif ($item['type'] == 1) {
            $itemOne = (new model\Item())->one($item['page_id']);
            $item['page'] = $itemOne ? $itemOne['name'] : '此商品页已被删除';
        } elseif ($item['type'] == 2) {
            $categoryOne = (new model\Category())->one($item['page_id']);
            $item['page'] = $categoryOne ? $categoryOne['name'] : '此品牌分类已被删除';
        } elseif ($item['type'] == 3) {
            $brandOne = (new model\Brand())->one($item['page_id']);
            $item['page'] = $brandOne ? $brandOne['name'] : '此品牌已被删除';
        }
        $item['type'] = $this->type[$item['type']];
        if ($item['scene_id']) {
            $item['scene'] = Config::get('wxxcx_scene.' . $item['scene_id'], '未知');
        } else {
            $item['scene'] = '';
        }
        $item['date1'] = dateFormat($item['date1']);
        $item['date2'] = dateFormat($item['date2']);
        return $item;
    }
}
