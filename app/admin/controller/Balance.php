<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Request;
use think\facade\View;

class Balance extends Base
{
    private array $type = [['green', '结算'], ['red', '提现']];

    public function index()
    {
        $Balance = new model\Balance();
        $balanceAll = $Balance->all();
        if (Request::isAjax()) {
            foreach ($balanceAll as $key => $value) {
                $balanceAll[$key] = $this->listItem($value);
            }
            return $balanceAll->items() ? json_encode($balanceAll->items()) : '';
        }
        View::assign(['Total' => $balanceAll->total(), 'Sum' => $Balance->sum()]);
        Html::manager(Request::get('manager_id'), 3);
        Html::typeSelect($this->type, Request::post('type', -1));
        return $this->view();
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Balance = new model\Balance();
            $balanceOne = $Balance->one();
            if (!$balanceOne) {
                return showTip('不存在此结算记录！', 0);
            }
            if (Request::get('action') == 'do') {
                $balanceModify = $Balance->modify();
                return is_numeric($balanceModify) ?
                    showTip(['msg' => '结算记录修改成功！', 'data' => $this->listItem($Balance->one())]) :
                    showTip($balanceModify, 0);
            }
            $managerOne = (new model\Manager())->one($balanceOne['manager_id']);
            $balanceOne['manager'] = $managerOne ? $managerOne['name'] : '此分销商已被删除';
            $balanceOne['type'] = '<span class="' . $this->type[$balanceOne['price'] < 0][0] . '">' .
                $this->type[$balanceOne['price'] < 0][1] . '</span>';
            View::assign(['One' => $balanceOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Balance = new model\Balance();
            if (Request::post('id')) {
                if (!$Balance->one()) {
                    return showTip('不存在此结算记录！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$Balance->one($value)) {
                        return showTip('不存在您勾选的结算记录！', 0);
                    }
                }
            }
            return $Balance->remove() ? showTip('结算记录删除成功！') : showTip('结算记录删除失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $managerOne = (new model\Manager())->one($item['manager_id']);
        $item['manager'] = $managerOne ? $managerOne['name'] : '此分销商已被删除';
        $item['type'] = '<span class="' . $this->type[$item['price'] < 0][0] . '">' .
            $this->type[$item['price'] < 0][1] . '</span>';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
