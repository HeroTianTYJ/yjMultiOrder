<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Request;
use think\facade\View;

class Bill extends Base
{
    public function index()
    {
        $Bill = new model\Bill();
        $billAll = $Bill->all();
        if (Request::isAjax()) {
            foreach ($billAll as $key => $value) {
                $billAll[$key] = $this->listItem($value);
            }
            return $billAll->items() ? json_encode($billAll->items()) : '';
        }
        $param = '';
        foreach (Request::get() as $key => $value) {
            if (!in_array($key, ['page', 'order'])) {
                $param .= '&' . $key . '=' . $value;
            }
        }
        View::assign([
            'Total' => $billAll->total(),
            'New' => $Bill->newer(),
            'Param' => '?' . substr($param, 1)
        ]);
        Html::manager(Request::get('manager_id'));
        Html::billSort(Request::get('bill_sort_id'));
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $billAdd = (new model\Bill())->add();
                if (is_numeric($billAdd)) {
                    return $billAdd > 0 ? showTip('账单添加成功！') : showTip('账单添加失败！', 0);
                } else {
                    return showTip($billAdd, 0);
                }
            }
            Html::billSort();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Bill = new model\Bill();
            $billOne = $Bill->one();
            if (!$billOne) {
                return showTip('不存在此账单！', 0);
            }
            if (Request::get('action') == 'do') {
                $billModify = $Bill->modify($billOne['text_id_note']);
                if (is_numeric($billModify)) {
                    $Bill->modify2(10);
                    return showTip(['msg' => '账单修改成功！', 'data' => $this->listItem($Bill->one())]);
                } else {
                    return showTip($billModify, 0);
                }
            }
            Html::billSort($billOne['bill_sort_id']);
            $billOne['note'] = (new model\Text())->content($billOne['text_id_note']);
            View::assign(['One' => $billOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update2()
    {
        if (Request::isAjax()) {
            $billModify = (new model\Bill())->modify2(Request::post('rows'));
            return $billModify == 1 ? showTip('账单当前收支余更新成功！') : showTip($billModify, 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function detail()
    {
        if (Request::post('id')) {
            $billOne = (new model\Bill())->one();
            if (!$billOne) {
                return showTip('不存在此账单！', 0);
            }
            $managerOne = (new model\Manager())->one($billOne['manager_id']);
            $billOne['manager'] = $managerOne ? $managerOne['name'] : '此管理员已被删除';
            $billSortOne = (new model\BillSort())->one($billOne['bill_sort_id']);
            $billOne['bill_sort'] = $billSortOne ? $billSortOne['name'] : '此分类已被删除';
            $billOne['in'] = number_format($billOne['in_price'] * $billOne['in_count'], 2, '.', '');
            $billOne['in_out'] = number_format($billOne['in_price_out'] * $billOne['in_count_out'], 2, '.', '');
            $billOne['out'] = number_format($billOne['out_price'] * $billOne['out_count'], 2, '.', '');
            $billOne['out_in'] = number_format($billOne['out_price_in'] * $billOne['out_count_in'], 2, '.', '');
            $billOne['sum'] = number_format($billOne['in_price'] * $billOne['in_count'] - $billOne['in_price_out'] *
                $billOne['in_count_out'] - $billOne['out_price'] * $billOne['out_count'] + $billOne['out_price_in'] *
                $billOne['out_count_in'], 2, '.', '');
            $billOne['note'] = (new model\Text())->content($billOne['text_id_note']);
            View::assign(['One' => $billOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Bill = new model\Bill();
            $textId = [];
            if (Request::post('id')) {
                $billOne = $Bill->one();
                if (!$billOne) {
                    return showTip('不存在此账单！', 0);
                }
                $textId[] = $billOne['text_id_note'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $billOne = $Bill->one($value);
                    if (!$billOne) {
                        return showTip('不存在您勾选的账单！', 0);
                    }
                    $textId[] = $billOne['text_id_note'];
                }
            }
            if ($Bill->remove()) {
                $Bill->modify2(10);
                (new model\Text())->remove($textId);
                return showTip('账单删除成功！');
            } else {
                return showTip('账单删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function output()
    {
        if (Request::isAjax()) {
            $Bill = new model\Bill();
            if (Request::post('type') == 0) {
                return $this->outputDo($Bill->all2());
            } else {
                return $this->outputDo($Bill->all3());
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function outputDo($billAll)
    {
        $output = '"账单名称","所属管理员","账单分类","收入单价","收入数量","进价/退款单价","进价/退款数量",' .
            '"支出单价","支出数量","退款单价","退款数量","合计","当前收入","当前支出","当前余额","备注","时间",';
        if (count($billAll)) {
            $Manager = new model\Manager();
            $BillSort = new model\BillSort();
            foreach ($billAll as $value) {
                $managerOne = $Manager->one($value['manager_id']);
                $billSortOne = $BillSort->one($value['bill_sort_id']);
                $output .= "\r\n" . '"' . $value['name'] . '","' . ($managerOne ? $managerOne['name'] : '此管理员已被删除') .
                    '","' . ($billSortOne ? $billSortOne['name'] : '此分类已被删除') . '","' . $value['in_price'] . '","' .
                    $value['in_count'] . '","' . $value['in_price_out'] . '","' . $value['in_count_out'] . '","' .
                    $value['out_price'] . '","' . $value['out_count'] . '","' . $value['out_price_in'] . '","' .
                    $value['out_count_in'] . '","' . ($value['in_price'] * $value['in_count'] - $value['in_price_out'] *
                        $value['in_count_out'] - $value['out_price'] * $value['out_count'] + $value['out_price_in'] *
                        $value['out_count_in']) . '","' . $value['all_in'] . '","' . $value['all_out'] . '","' .
                    $value['all_add'] . '","' . (new model\Text())->content($value['text_id_note']) . '","' .
                    dateFormat($value['date']) . '",';
            }
        }
        return json_encode(['extension' => 'csv', 'filename' => 'bill_' . date('YmdHis') . '.csv', 'file' => $output]);
    }

    private function listItem($item)
    {
        $item['name'] = '<span title="' . $item['name'] . '">' . keyword(truncate($item['name'], 0, 20)) . '</span>';
        $item['in'] = $item['in_price'] * $item['in_count'];
        $item['in_out'] = $item['in_price_out'] * $item['in_count_out'];
        $item['out'] = $item['out_price'] * $item['out_count'];
        $item['out_in'] = $item['out_price_in'] * $item['out_count_in'];
        $item['add'] = $item['in'] . '-' . $item['in_out'] . '-' . $item['out'] . '+' . $item['out_in'] . '=' .
            ($item['in'] - $item['in_out'] - $item['out'] + $item['out_in']) . '元';
        $managerOne = (new model\Manager())->one($item['manager_id']);
        $item['manager'] = $managerOne ? $managerOne['name'] : '此管理员已被删除';
        $billSortOne = (new model\BillSort())->one($item['bill_sort_id']);
        $item['bill_sort'] = $billSortOne ? $billSortOne['name'] : '此分类已被删除';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
