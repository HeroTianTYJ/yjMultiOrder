<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
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
        Html::manager(Request::get('manager_id'), 3);
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

    public function output()
    {
        if (Request::isAjax()) {
            $output = '"IP","推广分销商","微信小程序","页面类型","页面名称","访问场景","当日次数","第一次","最后一次",';
            $VisitWxxcx = new model\VisitWxxcx();
            $visitWxxcxAll = $VisitWxxcx->all2();
            if ($visitWxxcxAll) {
                $Manager = new model\Manager();
                $Wxxcx = new model\Wxxcx();
                $Lists = new model\Lists();
                $Item = new model\Item();
                $Category = new model\Category();
                $Brand = new model\Brand();
                $scene = Config::get('app.xcx_scene');
                foreach ($visitWxxcxAll as $value) {
                    if ($value['manager_id']) {
                        $managerOne = $Manager->one($value['manager_id']);
                        $managerName = $managerOne ? $managerOne['name'] : '此分销商已被删除';
                    } else {
                        $managerName = '';
                    }
                    $page = $type = '';
                    if ($value['type'] == 0) {
                        $listsOne = $Lists->one($value['page_id']);
                        $page = $listsOne ? $listsOne['name'] : '此列表页已被删除';
                        $type = '列表页';
                    } elseif ($value['type'] == 1) {
                        $itemOne = $Item->one2($value['page_id']);
                        $page = $itemOne ? $itemOne['name'] : '此商品页已被删除';
                        $type = '商品页';
                    } elseif ($value['type'] == 2) {
                        $categoryOne = $Category->one($value['page_id']);
                        $page = $categoryOne ? $categoryOne['name'] : '此品牌分类已被删除';
                        $type = '品牌分类页';
                    } elseif ($value['type'] == 3) {
                        $brandOne = $Brand->one($value['page_id']);
                        $page = $brandOne ? $brandOne['name'] : '此品牌已被删除';
                        $type = '品牌详情页';
                    }
                    $wxxcxOne = $Wxxcx->one($value['wxxcx_id']);
                    $output .= "\r\n" . '"' . $value['ip'] . ' -- ' . QQWry::getAddress($value['ip']) . '","' .
                        $managerName . '","' . ($wxxcxOne ? $wxxcxOne['name'] : '此微信小程序已被删除') . '","' . $type . '","' .
                        $page . '","' . ($scene[$value['scene_id']] ?? '未知') . '","' . $value['count'] . '","' .
                        dateFormat($value['date1']) . '","' . dateFormat($value['date2']) . '",';
                }
            }
            if (
                file_put_contents(
                    ROOT_DIR . '/' . Config::get('dir.output') . 'visit_wxxcx_' . date('YmdHis') . '.csv',
                    mb_convert_encoding($output, 'GBK', 'UTF-8')
                )
            ) {
                $VisitWxxcx->truncate();
                return showTip('访问统计导出成功！');
            } else {
                return showTip('访问统计导出失败，请检查' . Config::get('dir.output') . '目录权限！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['ip'] = keyword($item['ip']) . '<br>' . QQWry::getAddress($item['ip']);
        if ($item['manager_id']) {
            $managerOne = (new model\Manager())->one($item['manager_id']);
            $item['manager'] = $managerOne ? $managerOne['name'] : '此分销商已被删除';
        } else {
            $item['manager'] = '-';
        }
        $wxxcxOne = (new model\Wxxcx())->one($item['wxxcx_id']);
        $item['wxxcx'] = $wxxcxOne ? $wxxcxOne['name'] : '此微信小程序已被删除';
        if ($item['type'] == 0) {
            $listsOne = (new model\Lists())->one($item['page_id']);
            $item['page'] = $listsOne ? $listsOne['name'] : '此列表页已被删除';
        } elseif ($item['type'] == 1) {
            $itemOne = (new model\Item())->one2($item['page_id']);
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
