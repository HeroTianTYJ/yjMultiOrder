<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Category extends Base
{
    public function index()
    {
        $Category = new model\Category();
        if (Request::get('parent_id')) {
            $categoryOne = $Category->one(Request::get('parent_id'));
            if (!$categoryOne) {
                return $this->failed('不存在此品牌分类！');
            }
            if ($categoryOne['parent_id'] != 0) {
                return $this->failed('只能查看一级品牌分类下的子品牌分类！');
            }
            View::assign(['ParentName' => $categoryOne['name']]);
        }
        $categoryAll = $Category->all();
        if (Request::isAjax()) {
            foreach ($categoryAll as $key => $value) {
                $categoryAll[$key] = $this->listItem($value);
            }
            return $categoryAll->items() ? json_encode($categoryAll->items()) : '';
        }
        View::assign(['Total' => $categoryAll->total()]);
        Html::wxxcx(Request::get('wxxcx_id'));
        Html::manager(Request::get('manager_id'), 3);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            $Category = new model\Category();
            if (Request::get('parent_id')) {
                $categoryOne = $Category->one(Request::get('parent_id'));
                if (!$categoryOne) {
                    return showTip('不存在此品牌分类！', 0);
                }
                if ($categoryOne['parent_id'] != 0) {
                    return showTip('只能在一级品牌分类下添加子品牌分类！', 0);
                }
            }
            if (Request::get('action') == 'do') {
                $categoryAdd = $Category->add();
                if (is_numeric($categoryAdd)) {
                    return $categoryAdd > 0 ? showTip('品牌分类添加成功！') : showTip('品牌分类添加失败！', 0);
                } else {
                    return showTip($categoryAdd, 0);
                }
            }
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function multi()
    {
        if (Request::isAjax()) {
            $Category = new model\Category();
            if (Request::get('parent_id')) {
                $categoryOne = $Category->one(Request::get('parent_id'));
                if (!$categoryOne) {
                    return showTip('不存在此品牌分类！', 0);
                }
                if ($categoryOne['parent_id'] != 0) {
                    return showTip('只能在一级品牌分类下添加子品牌分类！', 0);
                }
            }
            if (Request::get('action') == 'do') {
                $categoryMulti = $Category->multi();
                if (is_numeric($categoryMulti)) {
                    return $categoryMulti > 0 ? showTip('品牌分类批量添加成功！') : showTip('品牌分类批量添加失败！', 0);
                } else {
                    return showTip($categoryMulti, 0);
                }
            }
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Category = new model\Category();
            $categoryOne = $Category->one();
            if (!$categoryOne) {
                return showTip('不存在此品牌分类！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo')) {
                    return showTip('演示站，品牌分类无法修改！', 0);
                }
                $categoryModify = $Category->modify();
                if (is_numeric($categoryModify)) {
                    if (Request::post('parent_id') && $categoryOne['parent_id'] == 0) {
                        (new model\Click())->remove([Config::get('page.category'), Config::get('page.category_wxxcx')]);

                        $WxxcxShare = new model\WxxcxShare();
                        foreach ($WxxcxShare->all(Config::get('page.category')) as $value) {
                            $qrcode = ROOT_DIR . '/download/qrcode/' . $value['id'] . '.jpg';
                            if (is_file($qrcode)) {
                                unlink($qrcode);
                            }
                        }
                        $WxxcxShare->remove(Config::get('page.category'));
                    }
                    return showTip([
                        'msg' => '品牌分类修改成功！',
                        'data' => $this->listItem($Category->one()),
                        'reload' => Request::post('parent_id') != $categoryOne['parent_id']
                    ]);
                } else {
                    return showTip($categoryModify, 0);
                }
            }
            Html::category1($categoryOne['parent_id']);
            View::assign(['One' => $categoryOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function sort()
    {
        if (Request::isAjax()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，品牌分类无法设置排序！', 0);
            }
            $Category = new model\Category();
            foreach (Request::post('sort') as $key => $value) {
                if (is_numeric($value)) {
                    $Category->sort($key, $value);
                }
            }
            return showTip('品牌分类排序成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isDefault()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo')) {
                return showTip('演示站，品牌分类无法设置默认！', 0);
            }
            $Category = new model\Category();
            $categoryOne = $Category->one();
            if (!$categoryOne) {
                return showTip('不存在此品牌分类！', 0);
            }
            if ($categoryOne['parent_id'] != 0) {
                return showTip('不能将子品牌分类设为默认！', 0);
            }
            return $Category->isDefault() ? showTip('设置默认品牌分类成功！') : showTip('设置默认品牌分类失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isView()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo')) {
                return showTip('演示站，品牌分类无法设置上下架！', 0);
            }
            $Category = new model\Category();
            $categoryOne = $Category->one();
            if (!$categoryOne) {
                return showTip('不存在此品牌分类！', 0);
            }
            if ($categoryOne['is_view'] == 0) {
                return $Category->isView(1) ? showTip('品牌分类上架成功！') : showTip('品牌分类上架失败！', 0);
            } else {
                return $Category->isView(0) ? showTip('品牌分类下架成功！') : showTip('品牌分类下架失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，品牌分类无法删除！', 0);
            }
            $Category = new model\Category();
            if (Request::post('id')) {
                if (!$Category->one()) {
                    return showTip('不存在此品牌分类！', 0);
                }
                if ($Category->one2(Request::post('id'))) {
                    return showTip('此品牌分类下有子品牌分类，无法删除！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$Category->one($value)) {
                        return showTip('不存在您勾选的品牌分类！', 0);
                    }
                    if ($Category->one2($value)) {
                        return showTip('此品牌分类下有子品牌分类，无法删除！', 0);
                    }
                }
            }
            if ($Category->remove()) {
                (new model\Click())->remove([Config::get('page.category'), Config::get('page.category_wxxcx')]);

                $WxxcxShare = new model\WxxcxShare();
                foreach ($WxxcxShare->all(Config::get('page.category')) as $value) {
                    $qrcode = ROOT_DIR . '/download/qrcode/' . $value['id'] . '.jpg';
                    if (is_file($qrcode)) {
                        unlink($qrcode);
                    }
                }
                $WxxcxShare->remove(Config::get('page.category'));

                return showTip('品牌分类删除成功！');
            } else {
                return showTip('品牌分类删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $item['date'] = dateFormat($item['date']);
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.category'), Request::get('manager_id', 0), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(
            Config::get('page.category_wxxcx'),
            Request::get('manager_id', 0),
            $item['id'],
            Request::get('wxxcx_id', 0)
        );
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        if (Request::get('parent_id', 0) == 0) {
            $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'category/' . $item['id'] .
                '.html';
        }
        return $item;
    }
}
