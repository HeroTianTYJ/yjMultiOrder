<?php

namespace app\admin\controller;

use app\admin\model;
use think\facade\Request;
use think\facade\View;

class Flow extends Base
{
    public function index()
    {
        $flowAll = (new model\Flow())->all();
        if (Request::isAjax()) {
            foreach ($flowAll as $key => $value) {
                $flowAll[$key] = $this->listItem($value);
            }
            return $flowAll->items() ? json_encode($flowAll->items()) : '';
        }
        View::assign(['Total' => $flowAll->total()]);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $flowAdd = (new model\Flow())->add();
                if (is_numeric($flowAdd)) {
                    return $flowAdd > 0 ? showTip('资金流动添加成功！') : showTip('资金流动添加失败！', 0);
                } else {
                    return showTip($flowAdd, 0);
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
            $Flow = new model\Flow();
            $flowOne = $Flow->one();
            if (!$flowOne) {
                return showTip('不存在此资金流动！', 0);
            }
            if (Request::get('action') == 'do') {
                $flowModify = $Flow->modify($flowOne['text_id_note']);
                return is_numeric($flowModify) ?
                    showTip(['msg' => '资金流动修改成功！', 'data' => $this->listItem($Flow->one())]) :
                    showTip($flowModify, 0);
            }
            $flowOne['note'] = (new model\Text())->content($flowOne['text_id_note']);
            View::assign(['One' => $flowOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Flow = new model\Flow();
            $textId = [];
            if (Request::post('id')) {
                $flowOne = $Flow->one();
                if (!$flowOne) {
                    return showTip('不存在此资金流动！', 0);
                }
                $textId[] = $flowOne['text_id_note'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $flowOne = $Flow->one($value);
                    if (!$flowOne) {
                        return showTip('不存在您勾选的资金流动！', 0);
                    }
                    $textId[] = $flowOne['text_id_note'];
                }
            }
            if ($Flow->remove()) {
                (new model\Text())->remove($textId);
                return showTip('资金流动删除成功！');
            } else {
                return showTip('资金流动删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $item['price'] = keyword($item['price']);
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
