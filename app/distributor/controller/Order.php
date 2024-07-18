<?php

namespace app\distributor\controller;

use app\common\controller\Auth;
use app\distributor\model;
use app\distributor\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\facade\View;

class Order extends Base
{
    public array $isCommission = [['red', '未结算'], ['green', '已结算']];

    public function index()
    {
        $orderAll = (new model\Order())->all();
        if (Request::isAjax()) {
            foreach ($orderAll as $key => $value) {
                $orderAll[$key] = $this->listItem($value);
            }
            return $orderAll->items() ? json_encode($orderAll->items()) : '';
        }
        View::assign(['Total' => $orderAll->total()]);
        Html::product(Request::get('product_id'));
        Html::orderIsCommissionSelect($this->isCommission, Request::get('is_commission', -1), 1);
        Html::orderStateSelect(Request::get('order_state_id'), 1);
        Html::orderPaymentSelect(Request::get('payment_id'), 1);
        Html::express(Request::get('express_id'), 1);
        Html::template2(Request::get('template_id'), 0, 1);
        return $this->view();
    }

    public function add()
    {
        return (new Auth())->addOrder();
    }

    public function balance()
    {
        if (Request::isAjax()) {
            $Order = new model\Order();
            $orderBalance = $Order->balance();
            if (Request::get('action') == 'do') {
                if (!$orderBalance[1]) {
                    return showTip('暂无可结算订单！', 0);
                }
                if ((new model\Balance())->add($orderBalance[0]['sum'])) {
                    return is_numeric($Order->modify(arrToStr($orderBalance[1], 'id'))) ?
                        showTip('分销佣金结算成功，<a href="' . Route::buildUrl('/balance/index') . '">点击此处</a>可查看结算记录，或进行提现！') :
                        showTip('分销佣金结算失败！', 0);
                } else {
                    return showTip('分销佣金结算失败！', 0);
                }
            }
            View::assign(['One' => $orderBalance[0]]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function detail()
    {
        if (Request::post('id')) {
            $Order = new model\Order();
            $orderOne = $Order->one();
            if (!$orderOne) {
                return showTip('不存在此订单，或没有此订单的管理权限！', 0);
            }

            $templateOne = (new model\Template())->one($orderOne['template_id']);
            $orderOne['template'] = $templateOne ? $templateOne['name'] : '此下单模板已被删除';

            $productOne = (new model\Product())->one($orderOne['product_id']);
            $orderOne['product'] = $productOne ? $productOne['name'] . '（' .
                ($productOne['low_price'] != '0.00' && $productOne['high_price'] != '0.00' ?
                    $productOne['low_price'] . '元～' . $productOne['high_price'] . '元' :
                    $productOne['price'] . '元') . '）' : '此商品已被删除';

            $orderOne['attr'] = str_replace("\r\n", '<br>', $orderOne['attr']);
            $orderOne['total'] = number_format($orderOne['price'] * $orderOne['count'], 2, '.', '');
            $orderOne['commission'] = number_format($orderOne['commission'] * $orderOne['count'], 2, '.', '');
            $orderOne['is_commission'] = '<span class="' . $this->isCommission[$orderOne['is_commission']][0] . '">' .
                $this->isCommission[$orderOne['is_commission']][1] . '</span>';

            $orderStateOne = (new model\OrderState())->one($orderOne['order_state_id']);
            $orderOne['order_state'] = $orderStateOne ?
                '<span style="color:' . $orderStateOne['color'] . ';">' . $orderStateOne['name'] . '</span>' :
                '此状态已被删除';

            if ($orderOne['express_id']) {
                $expressOne = (new model\Express())->one($orderOne['express_id']);
                $orderOne['express_name'] = $expressOne ? $expressOne['name'] : '此快递公司已被删除';
                $orderOne['express_code'] = $expressOne ? $expressOne['code'] : '';
            } else {
                $orderOne['express_name'] = $orderOne['express_code'] = '';
            }

            View::assign(['One' => $orderOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['order_id'] = keyword($item['order_id']);

        $templateOne = (new model\Template())->one($item['template_id']);
        $item['template'] = $templateOne ? $templateOne['name'] : '此下单模板已被删除';

        $productOne = (new model\Product())->one($item['product_id']);
        $item['product'] = $productOne ?
            $productOne['name'] . '（' . ($productOne['low_price'] != '0.00' && $productOne['high_price'] != '0.00' ?
                $productOne['low_price'] . '元～' . $productOne['high_price'] . '元' : $productOne['price'] . '元') . '）' :
            '此商品已被删除';

        $item['attr'] = keyword(str_replace("\r\n", '<br>', $item['attr']));
        $item['total'] = number_format($item['price'] * $item['count'], 2, '.', '');
        $item['payment'] = Config::get('payment.' . $item['payment_id']);

        $orderStateOne = (new model\OrderState())->one($item['order_state_id']);
        $item['order_state'] = $orderStateOne ?
            '<span style="color:' . $orderStateOne['color'] . ';">' . $orderStateOne['name'] . '</span>' :
            '此状态已被删除';

        $expressOne = (new model\Express())->one($item['express_id']);
        $item['express'] = ($expressOne ? $expressOne['name'] : '') .
            '<br><a href="https://www.kuaidi100.com/chaxun?com=' . ($expressOne ? $expressOne['code'] : '') . '&nu=' .
            $item['express_number'] . '" target="_blank">' . keyword($item['express_number']) . '</a>';

        $item['commission'] = $item['commission'] ? number_format($item['commission'] * $item['count'], 2, '.', '') .
            '元<br><span class="' . $this->isCommission[$item['is_commission']][0] . '">' .
            $this->isCommission[$item['is_commission']][1] . '</span>' : '';
        $item['date'] = dateFormat($item['date']);

        return $item;
    }
}
