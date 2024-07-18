<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
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
        Html::manager(Request::get('manager_id'), 3);
        Html::category2(Request::get('category_id'));
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $brandAdd = (new model\Brand())->add();
                if (is_numeric($brandAdd)) {
                    return $brandAdd > 0 ? showTip('品牌添加成功！') : showTip('品牌添加失败！', 0);
                } else {
                    return showTip($brandAdd, 0);
                }
            }
            Html::category2();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function multi()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $brandMulti = (new model\Brand())->multi();
                if (is_numeric($brandMulti)) {
                    return $brandMulti > 0 ? showTip('品牌批量添加成功！') : showTip('品牌批量添加失败！', 0);
                } else {
                    return showTip($brandMulti, 0);
                }
            }
            Html::category2();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Brand = new model\Brand();
            $brandOne = $Brand->one();
            if (!$brandOne) {
                return showTip('不存在此品牌！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo')) {
                    return showTip('演示站，品牌无法修改！', 0);
                }
                $brandModify = $Brand->modify();
                return is_numeric($brandModify) ?
                    showTip(['msg' => '品牌修改成功！', 'data' => $this->listItem($Brand->one())]) :
                    showTip($brandModify, 0);
            }
            Html::category2($brandOne['category_id']);
            View::assign(['One' => $brandOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function sort()
    {
        if (Request::isAjax()) {
            $Brand = new model\Brand();
            foreach (Request::post('sort') as $key => $value) {
                if (is_numeric($value)) {
                    $Brand->sort($key, $value);
                }
            }
            return showTip('品牌排序成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isView()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo')) {
                return showTip('演示站，无法设置上下架！', 0);
            }
            $Brand = new model\Brand();
            $brandOne = $Brand->one();
            if (!$brandOne) {
                return showTip('不存在此品牌！', 0);
            }
            if ($brandOne['is_view'] == 0) {
                return $Brand->isView(1) ? showTip('品牌上架成功！') : showTip('品牌上架失败！', 0);
            } else {
                return $Brand->isView(0) ? showTip('品牌下架成功！') : showTip('品牌下架失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，品牌无法删除！', 0);
            }
            $Brand = new model\Brand();
            if (Request::post('id')) {
                if (!$Brand->one()) {
                    return showTip('不存在此品牌！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$Brand->one($value)) {
                        return showTip('不存在您勾选的品牌！', 0);
                    }
                }
            }
            if ($Brand->remove()) {
                (new model\Click())->remove([Config::get('page.brand'), Config::get('page.brand_wxxcx')]);

                $WxxcxShare = new model\WxxcxShare();
                foreach ($WxxcxShare->all(Config::get('page.brand')) as $value) {
                    $qrcode = ROOT_DIR . '/download/qrcode/' . $value['id'] . '.jpg';
                    if (is_file($qrcode)) {
                        unlink($qrcode);
                    }
                }
                $WxxcxShare->remove(Config::get('page.brand'));

                return showTip('品牌删除成功！');
            } else {
                return showTip('品牌删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
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
        $item['date'] = dateFormat($item['date']);
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.brand'), Request::get('manager_id', 0), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(
            Config::get('page.brand_wxxcx'),
            Request::get('manager_id', 0),
            $item['id'],
            Request::get('wxxcx_id', 0)
        );
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'brand/' . $item['id'] . '.html';
        return $item;
    }
}
