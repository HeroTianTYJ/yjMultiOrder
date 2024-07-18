<?php

namespace app\distributor\controller;

use app\distributor\model;
use think\facade\Request;
use think\facade\View;
use yjrj\QQWry;

class Visit extends Base
{
    public function index()
    {
        $visitAll = (new model\Visit())->all();
        if (Request::isAjax()) {
            foreach ($visitAll as $key => $value) {
                $visitAll[$key] = $this->listItem($value);
            }
            return $visitAll->items() ? json_encode($visitAll->items()) : '';
        }
        View::assign(['Total' => $visitAll->total()]);
        return $this->view();
    }

    private function listItem($item)
    {
        $item['truncate_url'] = keyword(truncate($item['url'], 0, 28));
        $item['ip'] = keyword($item['ip']) . '<br>' . QQWry::getAddress($item['ip']);
        $item['date1'] = dateFormat($item['date1']);
        $item['date2'] = dateFormat($item['date2']);
        return $item;
    }
}
