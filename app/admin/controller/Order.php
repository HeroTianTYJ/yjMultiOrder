<?php

namespace app\admin\controller;

use app\admin\model;
use app\admin\library\Html;
use app\common\controller\Auth;
use Exception;
use PHPMailer\PHPMailer;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use yjrj\QQWry;
use yjrj\Sms;

class Order extends Base
{
    public array $output = ['订单号', '管理员/分销商', '下单模板', '姓名', '订购商品', '商品属性', '成交单价', '订购数量', '成交总价', '分销佣金', '分销结算',
        '联系电话', '详细地址', '备注', '电子邮箱', '下单IP', '下单时间', '支付方式', '支付订单号', '支付场景', '支付时间', '订单状态', '快递公司', '快递单号'];
    public array $search = ['关键词', '订购商品', '成交单价', '订购数量', '成交总价', '分销结算', '管理员/分销商', '下单时间', '支付方式', '支付时间', '订单状态',
        '快递公司', '下单模板'];
    public array $list = [['订单号', 100], ['管理员/分销商', 130], ['下单模板', 130], ['姓名', 85], ['订购商品', 130], ['商品属性', 130],
        ['成交单价', 70], ['订购数量', 70], ['成交总价', 70], ['分销佣金', 70], ['联系电话', 100], ['详细地址', 200], ['备注', 200, 0],
        ['电子邮箱', 150], ['下单IP', 120], ['下单时间', 80], ['支付方式', 80], ['支付订单号', 210], ['支付场景', 90], ['支付时间', 80],
        ['订单状态', 80], ['快递信息', 140]];
    public array $detail = ['订单号', '管理员/分销商', '下单模板', '姓名', '订购商品', '商品属性', '成交单价', '订购数量', '成交总价', '分销佣金', '分销结算',
        '联系电话', '所在地区', '详细地址', '备注', '电子邮箱', '支付方式', '支付订单号', '支付场景', '支付时间', '下单IP', '下单时间', '订单状态', '快递公司', '快递单号'];
    public array $isCommission = [['red', '未结算'], ['green', '已结算']];

    public function index()
    {
        $orderAll = (new model\Order())->all();
        if (Request::isAjax()) {
            foreach ($orderAll as $key => $value) {
                $orderAll[$key]['html'] = $this->listItem($value);
            }
            return $orderAll->items() ? json_encode($orderAll->items()) : '';
        }
        View::assign([
            'Search' => Html::orderSearch($this->search, $this->isCommission),
            'Th' => $this->listTh(),
            'Total' => $orderAll->total()
        ]);
        return $this->view('order/index');
    }

    public function add()
    {
        return (new Auth())->addOrder();
    }

    public function state()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $orderModify = (new model\Order())->modify2();
                return is_numeric($orderModify) ? showTip('订单状态修改成功！') : showTip($orderModify, 0);
            }
            Html::orderStateRadio();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function express()
    {
        if (Request::isAjax()) {
            $Order = new model\Order();
            if (Request::get('action') == 'do') {
                $orderModify = $Order->modify3();
                return is_numeric($orderModify) ? showTip('快递单号修改成功！') : showTip($orderModify, 0);
            }
            $orderIds = '';
            foreach ($Order->all4() as $value) {
                $orderIds .= $value['order_id'] . '
';
            }
            View::assign(['OrderIds' => substr($orderIds, 0, -2)]);
            Html::express(0, 1);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Order = new model\Order();
            $orderOne = $Order->one();
            if (!$orderOne) {
                return showTip('不存在此订单！', 0);
            }
            if (Request::get('action') == 'do') {
                $orderModify = $Order->modify();
                if (is_numeric($orderModify)) {
                    if (Request::post('send_mail') && Request::post('email')) {
                        if (Request::post('send_mail') == 1) {
                            $this->sendmail(
                                Request::post('email'),
                                Config::get('system.mail_pay_subject'),
                                Config::get('system.mail_pay_content')
                            );
                        } elseif (Request::post('send_mail') == 2) {
                            $this->sendmail(
                                Request::post('email'),
                                Config::get('system.mail_send_subject'),
                                Config::get('system.mail_send_content')
                            );
                        }
                    }
                    if (Request::post('send_sms') == 1) {
                        if (
                            Config::get('system.sms_bao_user') && Config::get('system.sms_bao_pass') &&
                            Config::get('system.sms_backend_content')
                        ) {
                            $smsBao = (new Sms([
                                'user' => Config::get('system.sms_bao_user'),
                                'pass' => Config::get('system.sms_bao_pass')
                            ]))->smsBao(
                                Request::post('tel'),
                                strip_tags($this->orderVariableReplace(Config::get('system.sms_backend_content')))
                            );
                            if (!is_numeric($smsBao)) {
                                return showTip('短信发送失败，错误信息：' . $smsBao, 0);
                            }
                        }
                    }
                    return showTip([
                        'msg' => '订单修改成功！',
                        'data' => ['id' => Request::post('id'), 'html' => $this->listItem($Order->one())]
                    ]);
                } else {
                    return showTip($orderModify, 0);
                }
            }
            Html::template2($orderOne['template_id'], 0, 1);
            Html::product($orderOne['product_id'], 0, true);
            Html::express($orderOne['express_id'], 1);
            Html::orderPaymentRadio($orderOne['payment_id']);
            Html::orderStateRadio($orderOne['order_state_id']);
            Html::orderIsCommissionRadio($this->isCommission, $orderOne['is_commission']);
            $managerOne = (new model\Manager())->one($orderOne['manager_id']);
            $orderOne['manager_level'] = $managerOne ? $managerOne['level'] : 0;
            $orderOne['pay_url'] = $this->payUrl($orderOne['order_id']);
            View::assign(['One' => $orderOne]);
            return $this->view('order/update');
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
                return showTip('不存在此订单！', 0);
            }

            $distributor = false;
            if ($orderOne['manager_id']) {
                $managerOne = (new model\Manager())->one($orderOne['manager_id']);
                if ($managerOne) {
                    $managerName = $managerOne['name'];
                    $distributor = $managerOne['level'] == 3;
                } else {
                    $managerName = '此管理员/分销商已被删除';
                }
            } else {
                $managerName = '终端用户';
            }
            $templateOne = (new model\Template())->one($orderOne['template_id']);
            $productOne = (new model\Product())->one($orderOne['product_id']);
            $orderStateOne = (new model\OrderState())->one($orderOne['order_state_id']);
            $payScene = '';
            if ($orderOne['order_state_id'] != 1) {
                if ($orderOne['payment_id'] == 2) {
                    $payScene = Config::get('pay_scene.alipay.' . $orderOne['pay_scene']);
                } elseif ($orderOne['payment_id'] == 3) {
                    $payScene = Config::get('pay_scene.wechat_pay.' . $orderOne['pay_scene']);
                }
            }
            if ($orderOne['express_id']) {
                $expressOne = (new model\Express())->one($orderOne['express_id']);
                $expressName = $expressOne ? $expressOne['name'] : '此快递公司已被删除';
                $expressCode = $expressOne ? $expressOne['code'] : '';
            } else {
                $expressName = $expressCode = '';
            }

            $data = [
                $orderOne['order_id'],
                $managerName,
                $templateOne ? $templateOne['name'] : '此下单模板已被删除',
                $orderOne['name'],
                $productOne ? $productOne['name'] . '（' . ($productOne['low_price'] != '0.00' &&
                    $productOne['high_price'] != '0.00' ? $productOne['low_price'] . '元～' . $productOne['high_price'] .
                        '元' : $productOne['price'] . '元') . '）' : '此商品已被删除',
                str_replace("\r\n", '<br>', $orderOne['attr']),
                $orderOne['price'] . '元',
                $orderOne['count'],
                number_format($orderOne['price'] * $orderOne['count'], 2, '.', '') . '元',
                $distributor && $orderOne['commission'] ?
                    number_format($orderOne['commission'] * $orderOne['count'], 2, '.', '') . '元' : '-',
                $distributor && $orderOne['commission'] ?
                    '<span class="' . $this->isCommission[$orderOne['is_commission']][0] . '">' .
                        $this->isCommission[$orderOne['is_commission']][1] . '</span>' : '-',
                $orderOne['tel'],
                $orderOne['province'] . ' ' . $orderOne['city'] . ' ' . $orderOne['county'] . ' ' . $orderOne['town'],
                $orderOne['address'],
                $orderOne['note'],
                $orderOne['email'],
                Config::get('payment.' . $orderOne['payment_id']),
                $orderOne['pay_id'],
                $payScene,
                $orderOne['pay_date'] ? dateFormat($orderOne['pay_date']) : '',
                $orderOne['ip'] . ' ' . QQWry::getAddress($orderOne['ip']),
                dateFormat($orderOne['date']),
                $orderStateOne ?
                    '<span style="color:' . $orderStateOne['color'] . ';">' . $orderStateOne['name'] . '</span>' :
                    '此订单状态已被删除',
                $expressName,
                $orderOne['express_number'] . ($expressCode && $orderOne['express_number'] ?
                    '，<a href="https://www.kuaidi100.com/chaxun?com=' . $expressCode . '&nu=' .
                    $orderOne['express_number'] . '" target="_blank">查询进度</a>' : '')
            ];

            $detailSort = [];
            if (Config::get('order_ui.detail_sort')) {
                $detailSort = Config::get('order_ui.detail_sort');
            } else {
                foreach ($this->detail as $key => $value) {
                    $detailSort[$key] = 1;
                }
            }

            $html = '';
            foreach ($detailSort as $key => $value) {
                if ($value && isset($data[$key])) {
                    $html .= '<tr><td>' . $this->detail[$key] . '：</td><td>' . $data[$key] . '</td></tr>';
                }
            }
            View::assign(['Detail' => $html]);
            return $this->view('order/detail');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，订单无法删除！', 0);
            }
            $Order = new model\Order();
            if (Request::post('id')) {
                if (!$Order->one()) {
                    return showTip('不存在此订单！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$Order->one($value)) {
                        return showTip('不存在您勾选的订单！', 0);
                    }
                }
            }
            return $Order->recycle() ? showTip('订单已被移入回收站！') : showTip('订单移入回收站失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function output()
    {
        if (Request::isAjax()) {
            $Order = new model\Order();
            if (Request::post('type') == 0) {
                return $this->outputDo($Order->all2(), Request::post('siwu', 0));
            } else {
                return $this->outputDo($Order->all3(), Request::post('siwu', 0));
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function outputDo($orderAll, $type)
    {
        $orderOutput = [];
        if ($type == 0) {
            if (Config::get('order_output')) {
                $orderOutput = Config::get('order_output');
            } else {
                foreach ($this->output as $key => $value) {
                    $orderOutput[$key] = 1;
                }
            }
            $output = '';
            foreach ($orderOutput as $key => $value) {
                if ($value) {
                    $output .= '"' . $this->output[$key] . '",';
                }
            }
        } else {
            $output = '"客户编号","单位名称","单位简称","联系地址","邮政编码","联系人","联系人手机","用户电话","用户传真","所属省份","所属地市","网址","备注","是否客户",' .
                '"是否供应商","开户银行","帐号","拼音码","国家","区镇","邮箱",';
        }
        if (count($orderAll)) {
            $Manager = new model\Manager();
            $Template = new model\Template();
            $Product = new model\Product();
            $Express = new model\Express();
            $OrderState = new model\OrderState();
            foreach ($orderAll as $value) {
                if ($type == 0) {
                    $distributor = false;
                    if ($value['manager_id']) {
                        $managerOne = $Manager->one($value['manager_id']);
                        if ($managerOne) {
                            $managerName = $managerOne['name'];
                            $distributor = $managerOne['level'] == 3;
                        } else {
                            $managerName = '此管理员/分销商已被删除';
                        }
                    } else {
                        $managerName = '终端用户';
                    }
                    $templateOne = $Template->one($value['template_id']);
                    $productOne = $Product->one($value['product_id']);
                    if ($value['express_id']) {
                        $expressOne = $Express->one($value['express_id']);
                        $expressName = $expressOne ? $expressOne['name'] : '此快递公司已被删除';
                    } else {
                        $expressName = '';
                    }
                    $orderStateOne = $OrderState->one($value['order_state_id']);
                    $payScene = '';
                    if ($value['order_state_id'] != 1) {
                        if ($value['payment_id'] == 2) {
                            $payScene = Config::get('pay_scene.alipay.' . $value['pay_scene']);
                        } elseif ($value['payment_id'] == 3) {
                            $payScene = Config::get('pay_scene.wechat_pay.' . $value['pay_scene']);
                        }
                    }
                    $data = [
                        "'" . $value['order_id'],
                        $managerName,
                        $templateOne ? $templateOne['name'] : '此下单模板已被删除',
                        $value['name'],
                        $productOne ? $productOne['name'] . '（' . ($productOne['low_price'] != '0.00' &&
                            $productOne['high_price'] != '0.00' ? $productOne['low_price'] . '元～' .
                                $productOne['high_price'] . '元' : $productOne['price'] . '元') . '）' : '此商品已被删除',
                        str_replace("\r\n", ' | ', $value['attr']),
                        $value['price'] . '元',
                        $value['count'],
                        number_format($value['price'] * $value['count'], 2, '.', '') . '元',
                        $distributor && $value['commission'] ?
                            number_format($value['commission'] * $value['count'], 2, '.', '') . '元' : '-',
                        $this->isCommission[$value['is_commission']][1],
                        "'" . $value['tel'],
                        $value['province'] . ' ' . $value['city'] . ' ' . $value['county'] . ' ' .
                            $value['town'] . ' ' . $value['address'],
                        $value['note'],
                        $value['email'],
                        $value['ip'] . ' -- ' . QQWry::getAddress($value['ip']),
                        dateFormat($value['date']),
                        Config::get('payment.' . $value['payment_id']),
                        "'" . $value['pay_id'],
                        $payScene,
                        $value['pay_date'] ? dateFormat($value['pay_date']) : '',
                        $orderStateOne ? $orderStateOne['name'] : '此订单状态已被删除',
                        $expressName,
                        $value['express_number']
                    ];
                    $output .= "\r\n";
                    foreach ($orderOutput as $k => $v) {
                        if ($v) {
                            $output .= '"' . $data[$k] . '",';
                        }
                    }
                } else {
                    $output .= "\r\n" . '"","","","' . $value['town'] . ' ' . $value['address'] . '","","' .
                        $value['name'] . '","","\'' . $value['tel'] . '","","' . $value['province'] . '","' .
                        $value['city'] . '","","' . $value['note'] . '","","","","","","","' . $value['county'] .
                        '","",';
                }
            }
        }
        return json_encode(['extension' => 'csv', 'filename' => 'order_' . date('YmdHis') . '.csv', 'file' => $output]);
    }

    private function listTh()
    {
        $list = [];
        if (Config::get('order_ui.list')) {
            $list = Config::get('order_ui.list');
        } else {
            foreach ($this->list as $key => $value) {
                $list[$key][0] = $value[1];
                $list[$key][1] = $value[2] ?? 1;
            }
        }
        $th = [];
        foreach ($list as $key => $value) {
            if ($value[1]) {
                $th[$key][0] = $value[0];
                $th[$key][1] = $this->list[$key][0];
            }
        }
        return $th;
    }

    private function listItem($item)
    {
        $list = [];
        if (Config::get('order_ui.list')) {
            $list = Config::get('order_ui.list');
        } else {
            foreach ($this->list as $key => $value) {
                $list[$key][0] = $value[1];
                $list[$key][1] = $value[2] ?? 1;
            }
        }

        $distributor = false;
        if ($item['manager_id']) {
            $managerOne = (new model\Manager())->one($item['manager_id']);
            if ($managerOne) {
                $managerName = $managerOne['name'];
                $distributor = $managerOne['level'] == 3;
            } else {
                $managerName = '此管理员/分销商已被删除';
            }
        } else {
            $managerName = '终端用户';
        }
        $templateOne = (new model\Template())->one($item['template_id']);
        $productOne = (new model\Product())->one($item['product_id']);
        $address = $item['province'] . ' ' . $item['city'] . ' ' . $item['county'] . ' ' . $item['town'] . ' ' .
            $item['address'];
        $expressOne = [];
        if ($item['express_id']) {
            $expressOne = (new model\Express())->one($item['express_id']);
        }
        $orderStateOne = (new model\OrderState())->one($item['order_state_id']);
        $payScene = '';
        if ($item['order_state_id'] != 1) {
            if ($item['payment_id'] == 2) {
                $payScene = Config::get('pay_scene.alipay.' . $item['pay_scene']);
            } elseif ($item['payment_id'] == 3) {
                $payScene = Config::get('pay_scene.wechat_pay.' . $item['pay_scene']);
            }
        }
        $data = [
            keyword($item['order_id']),
            $managerName,
            $templateOne ? $templateOne['name'] : '此下单模板已被删除',
            keyword($item['name']),
            $productOne ?
                $productOne['name'] . '（' . ($productOne['low_price'] != '0.00' && $productOne['high_price'] != '0.00' ?
                    $productOne['low_price'] . '元～' . $productOne['high_price'] . '元' : $productOne['price'] . '元')
                . '）' : '此商品已被删除',
            keyword(str_replace("\r\n", '<br>', $item['attr'])),
            $item['price'] . '元',
            $item['count'],
            number_format($item['price'] * $item['count'], 2, '.', '') . '元',
            $distributor && $item['commission'] ? number_format($item['commission'] * $item['count'], 2, '.', '') .
                '元<br><span class="' . $this->isCommission[$item['is_commission']][0] . '">' .
                $this->isCommission[$item['is_commission']][1] . '</span>' : '-',
            keyword($item['tel']),
            '<span title="' . $item['address'] . '">' . keyword(truncate($address, 0, 25)) . '</span>',
            $item['note'],
            keyword($item['email']),
            '<span title="' . QQWry::getAddress($item['ip']) . '">' . keyword($item['ip']) . '</span>',
            dateFormat($item['date']),
            Config::get('payment.' . $item['payment_id']),
            keyword($item['pay_id']),
            $payScene,
            $item['pay_date'] ? dateFormat($item['pay_date']) : '',
            $orderStateOne ?
                '<span style="color:' . $orderStateOne['color'] . ';">' . $orderStateOne['name'] . '</span>' :
                '此订单状态已被删除',
            ($expressOne ? $expressOne['name'] : '') . '<br><a href="https://www.kuaidi100.com/chaxun?com=' .
                ($expressOne ? $expressOne['code'] : '') . '&nu=' . $item['express_number'] .
                '" target="_blank">' . keyword($item['express_number']) . '</a>'
        ];
        $html = '';
        foreach ($list as $key => $value) {
            if ($value[1]) {
                $html .= '<td>' . $data[$key] . '</td>';
            }
        }
        return $html;
    }

    private function sendmail($address, $subject, $content)
    {
        try {
            $Smtp = new model\Smtp();
            if ($Smtp->count() > 0) {
                $smtpOne = $Smtp->one2();
                if ($smtpOne) {
                    $PHPMailer = new PHPMailer();
                    $PHPMailer->Host = $smtpOne[0]['smtp'];
                    $PHPMailer->Port = $smtpOne[0]['port'];
                    $PHPMailer->Username = $PHPMailer->From = $smtpOne[0]['email'];
                    $PHPMailer->Password = $smtpOne[0]['pass'];
                    $PHPMailer->FromName = $smtpOne[0]['from_name'];
                    $PHPMailer->addAddress($address, $PHPMailer->FromName);
                    $PHPMailer->Subject = $this->orderVariableReplace($subject);
                    $PHPMailer->Body = $this->orderVariableReplace($content);
                    $PHPMailer->send();
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function orderVariableReplace($content)
    {
        $productOne = (new model\Product())->one(Request::post('product_id'));
        $expressOne = [];
        if (Request::post('express_id')) {
            $expressOne = (new model\Express())->one(Request::post('express_id'));
        }
        $orderStateOne = (new model\OrderState())->one(Request::post('order_state_id'));
        $payScene = '';
        if (Request::post('order_state_id') != 1) {
            if (Request::post('payment_id') == 2) {
                $payScene = Config::get('pay_scene.alipay.' . Request::post('pay_scene'));
            } elseif (Request::post('payment_id') == 3) {
                $payScene = Config::get('pay_scene.wechat_pay.' . Request::post('pay_scene'));
            }
        }

        $variable = ['order_id', 'product_name', 'product_attr', 'product_price', 'product_count', 'product_total',
            'name', 'tel', 'province', 'city', 'county', 'town', 'address', 'note', 'email', 'ip', 'pay_url', 'payment',
            'pay_id', 'pay_scene', 'pay_date', 'order_state', 'express_name', 'express_id', 'express_url', 'date'];
        $replace = [
            Request::post('order_id'),
            $productOne ? $productOne['name'] : '',
            str_replace("\r\n", ' | ', Request::post('attr')),
            $productOne ? $productOne['price'] : '',
            Request::post('count'),
            $productOne ? number_format($productOne['price'] * Request::post('count'), 2, '.', '') : '0.00',
            Request::post('name'),
            Request::post('tel'),
            Request::post('province'),
            Request::post('city'),
            Request::post('county'),
            Request::post('town'),
            Request::post('address'),
            Request::post('note'),
            Request::post('email'),
            Request::post('ip') . ' ' . QQWry::getAddress(Request::post('ip')),
            Config::get('url.web1') . Config::get('system.index_php') . 'order/detail.html?order_id=' .
            Request::post('order_id') . '&pay=1',
            Config::get('payment.' . Request::post('payment_id')),
            Request::post('pay_id'),
            $payScene,
            Request::post('pay_date') ? dateFormat(Request::post('pay_date')) : '',
            $orderStateOne ?
                '<span style="color:' . $orderStateOne['color'] . ';">' . $orderStateOne['name'] . '</span>' : '',
            $expressOne ? $expressOne['name'] : '',
            Request::post('express_number'),
            'https://www.kuaidi100.com/chaxun?com=' . ($expressOne ? $expressOne['code'] : '') . '&nu=' .
                Request::post('express_number'),
            dateFormat(Request::post('date'))
        ];
        foreach ($variable as $key => $value) {
            $content = str_replace('{' . $value . '}', $replace[$key], $content);
        }
        return $content;
    }

    private function payUrl($orderId)
    {
        return [
            'alipay' => Config::get('url.web1') . Config::get('system.index_php') . 'pay/alipay/order_id/' . $orderId .
                '.html',
            'wechat_pay' => Config::get('url.web1') . Config::get('system.index_php') . 'pay/wechatPay/order_id/' .
                $orderId . '.html'
        ];
    }
}
