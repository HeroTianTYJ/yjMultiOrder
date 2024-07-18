<?php

namespace app\admin\controller;

use app\admin\model;
use app\admin\library\Html;
use think\facade\Request;
use think\facade\View;

class BillStatistic extends Base
{
    public function index()
    {
        if (Request::isAjax()) {
            $Bill = new model\Bill();

            $data = [];
            //今天
            $time = date('Y-m-d');
            $data[0]['time'] = '今天（' . $time . '）';
            $data[0]['data'] = $Bill->diyTime($time, $time);
            //昨天
            $time = date('Y-m-d', strtotime('-1 day'));
            $data[1]['time'] = '昨天（' . $time . '）';
            $data[1]['data'] = $Bill->diyTime($time, $time);
            //本周
            $time1 = date('Y-m-d', time() - date('w') * 86400);
            $time2 = date('Y-m-d', time() + (6 - date('w')) * 86400);
            $data[2]['time'] = '本周（' . $time1 . ' ～ ' . $time2 . '）';
            $data[2]['data'] = $Bill->diyTime($time1, $time2);
            //最近一周
            $time1 = date('Y-m-d', time() - 518400);
            $time2 = date('Y-m-d');
            $data[3]['time'] = '最近一周（' . $time1 . ' ～ ' . $time2 . '）';
            $data[3]['data'] = $Bill->diyTime($time1, $time2);
            //本月
            $time1 = date('Y-m') . '-01';
            $time2 = date('Y-m-t');
            $data[4]['time'] = '本月（' . $time1 . ' ～ ' . $time2 . '）';
            $data[4]['data'] = $Bill->diyTime($time1, $time2);
            //最近一月
            $time1 = date('Y-m-d', time() - 2592000);
            $time2 = date('Y-m-d');
            $data[5]['time'] = '最近一月（' . $time1 . ' ～ ' . $time2 . '）';
            $data[5]['data'] = $Bill->diyTime($time1, $time2);
            //今年
            $time1 = date('Y') . '-01-01';
            $time2 = date('Y') . '-12-31';
            $data[6]['time'] = '今年（' . $time1 . ' ～ ' . $time2 . '）';
            $data[6]['data'] = $Bill->diyTime($time1, $time2);
            //最近一年
            $time1 = date('Y-m-d', time() - 31449600);
            $time2 = date('Y-m-d');
            $data[7]['time'] = '最近一年（' . $time1 . ' ～ ' . $time2 . '）';
            $data[7]['data'] = $Bill->diyTime($time1, $time2);
            //总计
            $billOlder = $Bill->older();
            $billNewer = $Bill->newer();
            $data[8]['time'] = '总计（' . date('Y-m-d', $billOlder ? $billOlder['date'] : 0) . ' ～ ' .
                date('Y-m-d', $billNewer ? $billNewer['date'] : 0) . '）';
            $data[8]['data'] = $Bill->diyTime();

            return $data ? json_encode($data) : '';
        }
        View::assign(['Total' => 9]);
        Html::manager(Request::get('manager_id'));
        Html::billSort(Request::get('bill_sort_id'));
        return $this->view();
    }

    public function day()
    {
        return $this->dayMonthYear('%Y年%m月%d日');
    }

    public function month()
    {
        return $this->dayMonthYear('%Y年%m月');
    }

    public function year()
    {
        return $this->dayMonthYear('%Y年');
    }

    public function output()
    {
        if (Request::isAjax()) {
            $output = '"时间","收入笔数","收入金额","支出笔数","支出金额","合计笔数","合计金额",';
            switch (Request::post('time')) {
                case 1:
                    $time = '%Y年%m月';
                    break;
                case 2:
                    $time = '%Y年';
                    break;
                default:
                    $time = '%Y年%m月%d日';
            }
            foreach ((new model\Bill())->dayMonthYear($time, false) as $value) {
                $output .= "\r\n" . '"' . $value['time'] . '","' . $value['all_in_count'] . '","' .
                    $value['all_in_price'] . '","' . $value['all_out_count'] . '","' . $value['all_out_price'] . '","' .
                    $value['all_add_count'] . '","' . $value['all_add_price'] . '",';
            }
            return json_encode([
                'extension' => 'csv',
                'filename' => 'bill_statistic_' . date('YmdHis') . '.csv',
                'file' => $output
            ]);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function dayMonthYear($time)
    {
        $billDayMonthYear = (new model\Bill())->dayMonthYear($time);
        $billDayMonthYearTotal = $billDayMonthYear->total();
        if (Request::isAjax()) {
            $data = $billDayMonthYear->items();
            if ($data) {
                $allInCount = $allInPrice = $allOutCount = $allOutPrice = $allAddCount = $allAddPrice = 0;
                foreach ($billDayMonthYear as $value) {
                    $allInCount += $value['all_in_count'];
                    $allInPrice += $value['all_in_price'];
                    $allOutCount += $value['all_out_count'];
                    $allOutPrice += $value['all_out_price'];
                    $allAddCount += $value['all_add_count'];
                    $allAddPrice += $value['all_add_price'];
                }
                if ($billDayMonthYearTotal) {
                    $data[$billDayMonthYearTotal] = [
                        'time' => '合计',
                        'all_in_count' => $allInCount,
                        'all_in_price' => number_format($allInPrice, 2, '.', ''),
                        'all_out_count' => $allOutCount,
                        'all_out_price' => number_format($allOutPrice, 2, '.', ''),
                        'all_add_count' => $allAddCount,
                        'all_add_price' => number_format($allAddPrice, 2, '.', '')
                    ];
                }
                return json_encode($data);
            } else {
                return '';
            }
        }
        $param = '?';
        foreach (Request::get() as $key => $value) {
            if ($key != 'order') {
                $param .= '&' . $key . '=' . $value;
            }
        }
        View::assign(['Total' => $billDayMonthYearTotal, 'Param' => $param]);
        Html::manager(Request::get('manager_id'));
        Html::billSort(Request::get('bill_sort_id'));
        return $this->view();
    }
}
