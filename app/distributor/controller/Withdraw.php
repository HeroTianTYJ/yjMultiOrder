<?php

namespace app\distributor\controller;

use app\distributor\model;
use app\distributor\library\Html;
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
        Html::stateSelect($this->state, Request::get('state', -1));
        return $this->view();
    }

    private function listItem($item)
    {
        $item['price'] = keyword($item['price']);
        $item['state'] = '<span class="' . $this->state[$item['state']][0] . '">' . $this->state[$item['state']][1] .
            '</span>';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
