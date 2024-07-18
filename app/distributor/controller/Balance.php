<?php

namespace app\distributor\controller;

use app\distributor\library\Html;
use app\distributor\model;
use think\facade\Request;
use think\facade\Route;
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
        Html::typeSelect($this->type, Request::get('type', -1));
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            $Balance = new model\Balance();
            if (Request::get('action') == 'do') {
                $balanceAdd = $Balance->add2();
                if (is_numeric($balanceAdd)) {
                    if ($balanceAdd) {
                        return (new model\Withdraw())->add() ?
                            showTip('提现申请成功，请耐心等待财务为您打款，<a href="' . Route::buildUrl('/withdraw/index') .
                                '">点击此处</a>可查看提现记录！') :
                            showTip('提现申请失败！', 0);
                    } else {
                        return showTip('提现申请失败！', 0);
                    }
                } else {
                    return showTip($balanceAdd, 0);
                }
            }
            View::assign(['Sum' => $Balance->sum()]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['type'] = '<span class="' . $this->type[$item['price'] < 0][0] . '">' .
            $this->type[$item['price'] < 0][1] . '</span>';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
