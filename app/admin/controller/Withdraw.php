<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Request;
use think\facade\View;

class Withdraw extends Base
{
    private array $state = [['red', '等待提现'], ['green', '已提现']];

    public function index()
    {
        $withdrawAll = (new model\Withdraw())->all();
        if (Request::isAjax()) {
            foreach ($withdrawAll as $key => $value) {
                $withdrawAll[$key] = $this->listItem($value);
            }
            return $withdrawAll->items() ? json_encode($withdrawAll->items()) : '';
        }
        View::assign(['Total' => $withdrawAll->total()]);
        Html::manager(Request::get('manager_id'), 3);
        Html::stateSelect($this->state, Request::get('state', -1));
        return $this->view();
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Withdraw = new model\Withdraw();
            $withdrawOne = $Withdraw->one();
            if (!$withdrawOne) {
                return showTip('不存在此提现记录！', 0);
            }
            if (Request::get('action') == 'do') {
                $withdrawModify = $Withdraw->modify();
                return is_numeric($withdrawModify) ?
                    showTip(['msg' => '提现记录修改成功！', 'data' => $this->listItem($Withdraw->one())]) :
                    showTip($withdrawModify, 0);
            }
            $withdrawOne['manager'] = (new model\Manager())->one($withdrawOne['manager_id']);
            Html::stateRadio($this->state, $withdrawOne['state']);
            View::assign(['One' => $withdrawOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Withdraw = new model\Withdraw();
            if (Request::post('id')) {
                if (!$Withdraw->one()) {
                    return showTip('不存在此提现记录！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$Withdraw->one($value)) {
                        return showTip('不存在您勾选的提现记录！', 0);
                    }
                }
            }
            return $Withdraw->remove() ? showTip('提现记录删除成功！') : showTip('提现记录删除失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $managerOne = (new model\Manager())->one($item['manager_id']);
        $item['manager'] = $managerOne ? $managerOne['name'] : '此分销商已被删除';
        $item['price'] = keyword($item['price']);
        $item['state'] = '<span class="' . $this->state[$item['state']][0] . '">' . $this->state[$item['state']][1] .
            '</span>';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
