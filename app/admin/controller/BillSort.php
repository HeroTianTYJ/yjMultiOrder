<?php

namespace app\admin\controller;

use app\admin\model;
use app\admin\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class BillSort extends Base
{
    private array $type = [['green', '收入'], ['red', '支出']];

    public function index()
    {
        $billSortAll = (new model\BillSort())->all();
        if (Request::isAjax()) {
            foreach ($billSortAll as $key => $value) {
                $billSortAll[$key] = $this->listItem($value);
            }
            return $billSortAll->items() ? json_encode($billSortAll->items()) : '';
        }
        View::assign(['Total' => $billSortAll->total()]);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $billSortAdd = (new model\BillSort())->add();
                if (is_numeric($billSortAdd)) {
                    return $billSortAdd > 0 ? showTip('账单分类添加成功！') : showTip('账单分类添加失败！', 0);
                } else {
                    return showTip($billSortAdd, 0);
                }
            }
            Html::typeRadio($this->type);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $BillSort = new model\BillSort();
            $billSortOne = $BillSort->one();
            if (!$billSortOne) {
                return showTip('不存在此账单分类！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo') && Request::post('id') == 1) {
                    return showTip('演示站，id为1的账单分类无法修改！', 0);
                }
                $billSortModify = $BillSort->modify();
                return is_numeric($billSortModify) ?
                    showTip(['msg' => '账单分类修改成功！', 'data' => $this->listItem($BillSort->one())]) :
                    showTip($billSortModify, 0);
            }
            Html::typeRadio($this->type, $billSortOne['type']);
            View::assign(['One' => $billSortOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，账单分类无法删除！', 0);
            }
            $BillSort = new model\BillSort();
            if (Request::post('id')) {
                if (!$BillSort->one()) {
                    return showTip('不存在此账单分类！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$BillSort->one($value)) {
                        return showTip('不存在您勾选的账单分类！', 0);
                    }
                }
            }
            return $BillSort->remove() ? showTip('账单分类删除成功！') : showTip('账单分类删除失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function sort()
    {
        if (Request::isAjax()) {
            $BillSort = new model\BillSort();
            foreach (Request::post('sort') as $key => $value) {
                if (is_numeric($value)) {
                    $BillSort->sort($key, $value);
                }
            }
            return showTip('账单分类排序成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $item['type'] = '<span class="' . $this->type[$item['type']][0] . '">' . $this->type[$item['type']][1] .
            '</span>';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
