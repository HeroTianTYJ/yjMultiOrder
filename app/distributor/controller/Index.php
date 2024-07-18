<?php

namespace app\distributor\controller;

use app\distributor\model;
use think\facade\Config;
use think\facade\View;
use yjrj\QQWry;

class Index extends Base
{
    public function index()
    {
        $version = explode('|', Config::get('app.version'));
        $LoginRecordManager = new model\LoginRecordManager();
        $loginRecordManagerTotalCount = $LoginRecordManager->totalCount();
        $loginRecordManagerOne = $loginRecordManagerTotalCount > 1 ? $LoginRecordManager->one() : [];
        $Order = new model\Order();
        $data = [
            '系统信息' => [
                ['版本号', 'V' . $version[0]],
                ['更新时间', $version[1]]
            ],
            '个人信息' => [
                ['登录次数', $loginRecordManagerTotalCount],
                ['上次登录时间', $loginRecordManagerTotalCount > 1 ? dateFormat($loginRecordManagerOne['date']) : '首次登录'],
                ['上次登录IP', $loginRecordManagerTotalCount > 1 ?
                    $loginRecordManagerOne['ip'] . ' - ' . QQWry::getAddress($loginRecordManagerOne['ip']) : '首次登录']
            ],
            '订单' => [
                ['总数', $Order->totalCount()],
                ['待支付', $Order->totalCount(1)],
                ['待发货', $Order->totalCount(2)],
                ['已发货', $Order->totalCount(3)],
                ['已签收', $Order->totalCount(4)],
                ['售后中', $Order->totalCount(5)],
                ['交易关闭', $Order->totalCount(6)]
            ],
            '数据' => [
                ['今日网站PV', (new model\Visit())->totalCount()],
                ['今日小程序PV', (new model\VisitWxxcx())->totalCount()]
            ]
        ];
        foreach ($data as $key => $value) {
            $data[$key]['width'] = floor((100 / count($value) * 100)) / 100;
        }
        View::assign(['Data' => $data]);
        return $this->view();
    }
}
