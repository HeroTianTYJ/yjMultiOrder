<?php

namespace app\admin\library;

use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\facade\Session;
use think\facade\View;

class Html extends \app\common\library\Html
{
    public static function manager($id = 0, $type = 0)
    {
        self::selectDataset((new model\Manager())->all2($type), 'Manager', $id);
    }

    public static function manager2($id = 0)
    {
        $session = Session::get(Config::get('system.session_key_admin') . '.manage_info');
        return self::selectDataset(
            (new model\Manager())->all2(1),
            '',
            $id,
            $session['level'] == 1 || ($session['level'] == 2 && $session['order_permit'] != 1) ?
                '<option value="0" ' . ($id == 0 ? 'selected' : '') . '>终端客户</option>' : ''
        );
    }

    public static function permitManage($ids = [])
    {
        $PermitManage = new model\PermitManage();
        $isDefault = arrToStr($PermitManage->all5(), 'id');
        $ids = is_array($ids) ? $isDefault : $ids;
        $html = '';
        $permitManageAll = $PermitManage->all2();
        if (count($permitManageAll)) {
            $html .= '<table>';
            foreach ($permitManageAll as $value) {
                $html .= '<tr><td><div class="check-box"><label' . (in_array($value['id'], explode(',', $isDefault)) ?
                        ' class="red"' : '') . '><input type="checkbox" name="permit_manage_ids[]" ' .
                    (in_array($value['id'], explode(',', $ids)) ? 'checked' : '') . ' value="' . $value['id'] .
                    '">' . $value['name'] . '</label></div></td><td>';
                foreach ($PermitManage->all3($value['id']) as $v) {
                    $html .= '<div class="check-box"><label class="' . (in_array($v['id'], explode(',', $isDefault)) ?
                            ' red' : 'blue') . '"><input type="checkbox" name="permit_manage_ids[]" ' .
                        (in_array($v['id'], explode(',', $ids)) ? 'checked' : '') . ' value="' . $v['id'] . '">' .
                        $v['name'] . '</label></div>';
                }
                $html .= '</td></tr>';
            }
            $html .= '</table>';
        }
        View::assign(['PermitManage' => $html]);
    }

    public static function permitData($ids = [])
    {
        $PermitData = new model\PermitData();
        $isDefault = arrToStr($PermitData->all5(), 'id');
        $ids = is_array($ids) ? $isDefault : $ids;
        $html = '';
        $permitDataAll = $PermitData->all2();
        if (count($permitDataAll)) {
            $html .= '<table>';
            foreach ($permitDataAll as $value) {
                $html .= '<tr><td><div class="check-box"><label' . (in_array($value['id'], explode(',', $isDefault)) ?
                        ' class="red"' : '') . '><input type="checkbox" name="permit_data_ids[]" ' .
                    (in_array($value['id'], explode(',', $ids)) ? 'checked' : '') . ' value="' . $value['id'] .
                    '">' . $value['name'] . '</label></div></td><td>';
                foreach ($PermitData->all3($value['id']) as $v) {
                    $html .= '<div class="check-box"><label class="' . (in_array($v['id'], explode(',', $isDefault)) ?
                            ' red' : 'blue') . '"><input type="checkbox" name="permit_data_ids[]" ' .
                        (in_array($v['id'], explode(',', $ids)) ? 'checked' : '') . ' value="' . $v['id'] . '">' .
                        $v['name'] . '</label></div>';
                }
                $html .= '</td></tr>';
            }
            $html .= '</table>';
        }
        View::assign(['PermitData' => $html]);
    }

    public static function permitGroup($id = 0, $isDefault = 0)
    {
        self::selectDataset((new model\PermitGroup())->all2(), 'PermitGroup', $id, '', $isDefault);
    }

    public static function managerLevelSelect($level = [], $id = 0)
    {
        self::selectArray($level, 'Level', $id);
    }

    public static function managerLevelRadio($level = [], $id = 0)
    {
        self::radioArray($level, 'level', 'Level', $id);
    }

    public static function managerIsActivationSelect($isActivation = [], $id = 0)
    {
        self::selectArray($isActivation, 'IsActivation', $id);
    }

    public static function managerIsActivationRadio($isActivation = [], $id = 0)
    {
        self::radioArray($isActivation, 'is_activation', 'IsActivation', $id);
    }

    public static function managerOrderPermitSelect($orderPermit = [], $id = 0)
    {
        self::selectArray($orderPermit, 'OrderPermit', $id);
    }

    public static function managerOrderPermitRadio($orderPermit = [], $id = 0)
    {
        self::radioArray($orderPermit, 'order_permit', 'OrderPermit', $id);
    }

    public static function managerBillPermitSelect($billPermit = [], $id = 0)
    {
        self::selectArray($billPermit, 'BillPermit', $id);
    }

    public static function managerBillPermitRadio($billPermit = [], $id = 0)
    {
        self::radioArray($billPermit, 'bill_permit', 'BillPermit', $id);
    }

    public static function qq($qq = [], $id = 0)
    {
        self::selectArray($qq, 'Qq', $id);
    }

    public static function wechat($wechat = [], $id = 0)
    {
        self::selectArray($wechat, 'Wechat', $id);
    }

    public static function validateFileExtension($extension = [], $id = 0)
    {
        self::radioArray($extension, 'extension', 'Extension', $id);
    }

    public static function alipayScene($id = 0)
    {
        return self::selectArray(Config::get('pay_scene.alipay'), '', $id);
    }

    public static function wechatPayScene($id = 0)
    {
        return self::selectArray(Config::get('pay_scene.wechat_pay'), '', $id);
    }

    public static function orderStateRadio($id = 0)
    {
        $html = '';
        foreach ((new model\OrderState())->all2() as $value) {
            if ($id == 0) {
                $html .= '<div class="radio-box"><label style="color:' . $value['color'] .
                    ';"><input type="radio" name="order_state_id" value="' . $value['id'] . '" ' .
                    ($value['is_default'] ? 'checked' : '') . '>' . $value['name'] . '</label></div>';
            } else {
                $html .= '<div class="radio-box"><label style="color:' . $value['color'] .
                    ';"><input type="radio" name="order_state_id" value="' . $value['id'] . '" ' .
                    ($value['id'] == $id ? 'checked' : '') . '>' . $value['name'] . '</label></div>';
            }
        }
        View::assign(['OrderState' => $html]);
    }

    public static function productSort($id = 0)
    {
        self::selectDataset((new model\ProductSort())->all2(), 'ProductSort', $id);
    }

    public static function template($template = [], $id = 0)
    {
        $html = '';
        foreach ($template as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($key == $id ? 'selected' : '') . ' view="' .
                Route::buildUrl('/' . parse_name(Request::controller()) . '/templateView', ['id' => $key]) . '">' .
                $value . '</option>';
        }
        View::assign(['Template' => $html]);
    }

    public static function templateStyle($id = 0)
    {
        self::selectDataset((new model\TemplateStyle())->all2(), 'TemplateStyle', $id);
    }

    public static function field($ids = [])
    {
        $html = '';
        $Field = new model\Field();
        $isDefault = arrToStr($Field->all3(), 'id');
        $ids = is_array($ids) ? $isDefault : $ids;
        foreach ($Field->all2() as $value) {
            $html .= '<div class="check-box"><label' . (in_array($value['id'], explode(',', $isDefault)) ?
                    ' class="red"' : '') . '><input type="checkbox" name="field_ids[]" ' .
                (in_array($value['id'], explode(',', $ids)) ? 'checked' : '') . ' value="' . $value['id'] . '">' .
                $value['name'] . '</label></div>';
        }
        View::assign(['Field' => $html]);
    }

    public static function payment($ids = '', $default = 0)
    {
        $html = $html2 = '';
        foreach (Config::get('payment') as $key => $value) {
            $html .= '<div class="check-box"><label><input type="checkbox" name="payment_ids[]" value="' . $key . '" ' .
                (in_array($key, explode(',', $ids)) ? 'checked' : '') . '>' . $value . '</label></div>';
            $html2 .= '<option value="' . $key . '" ' . ($default == $key ? 'selected' : '') . '>' . $value .
                '</option>';
        }
        View::assign(['Payment' => $html, 'PaymentDefault' => $html2]);
    }

    public static function billSort($id = 0)
    {
        $BillSort = new model\BillSort();
        View::assign(['BillSort' => '<optgroup label="收入">' . self::selectDataset($BillSort->all2(0), '', $id) .
            '</optgroup><optgroup label="支出">' . self::selectDataset($BillSort->all2(1), '', $id) . '</optgroup>']);
    }

    public static function useImgBg($useImgBg = [], $id = 0)
    {
        self::radioArray($useImgBg, 'useImgBg', 'UseImgBg', $id);
    }

    public static function useCurve($useCurve = [], $id = 0)
    {
        self::radioArray($useCurve, 'useCurve', 'UseCurve', $id);
    }

    public static function useNoise($useNoise = [], $id = 0)
    {
        self::radioArray($useNoise, 'useNoise', 'UseNoise', $id);
    }

    public static function messageField($field = [], $ids = [])
    {
        self::checkboxArray($field, 'field', 'Field', $ids);
    }

    public static function captcha($id = 0)
    {
        $html = '<option value="0">不添加</option>';
        foreach (Config::get('captcha') as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($key == $id ? 'selected' : '') . '>' .
                $value['name'] . '</option>';
        }
        View::assign(['Captcha' => $html]);
    }

    public static function stateRadio($state = [], $id = 0)
    {
        self::radioArray($state, 'state', 'State', $id);
    }

    public static function codeType($codeType = [], $id = 0)
    {
        self::radioArray($codeType, 'code_type', 'CodeType', $id);
    }

    public static function messageBoard($id = 0)
    {
        self::selectDataset((new model\MessageBoard())->all2(), 'MessageBoard', $id);
    }

    public static function commentType($commentType = [], $ids = '-1')
    {
        $html = '';
        foreach ($commentType as $key => $value) {
            $html .= '<div class="check-box"><label><input type="checkbox" name="comment_type[]" value="' .
                $key . '" ' . (in_array($key, explode(',', $ids)) ? 'checked' : '') . ' class="comment_type">' .
                $value . '</label></div>';
        }
        View::assign(['CommentType' => $html]);
    }

    public static function columnSort($column = [], $ids = '')
    {
        $ids = $ids ? explode(',', $ids) : range(0, count($column) - 1);
        $html = '<ul>';
        foreach ($ids as $value) {
            $html .= '<li>' . $column[$value] . '<input type="hidden" name="sort[]" value="' . $value . '"></li>';
        }
        $html .= '</ul>';
        View::assign(['ColumnSort' => $html]);
    }

    public static function tagBg($id = 1)
    {
        $html = '';
        foreach (range(1, 7) as $value) {
            $html .= '<div class="radio-box"><label><input type="radio" name="tag_bg" value="' . $value . '" ' .
                ($value == $id ? 'checked' : '') . '><span style="background-position:0 -' . (($value - 1) * 20) .
                'px;"></span></label></div>';
        }
        View::assign(['TagBg' => $html]);
    }

    public static function productType($productType = [], $id = 0)
    {
        self::radioArray($productType, 'product_type', 'ProductType', $id);
    }

    public static function productViewType($productViewType = [], $id = 0)
    {
        self::radioArray($productViewType, 'product_view_type', 'ProductViewType', $id);
    }

    public static function isShowSearch($isShowSearch = [], $id = 0)
    {
        self::radioArray($isShowSearch, 'is_show_search', 'IsShowSearch', $id);
    }

    public static function isShowSend($isShowSend = [], $id = 0)
    {
        self::radioArray($isShowSend, 'is_show_send', 'IsShowSend', $id);
    }

    public static function isSmsVerify($isSmsVerify = [], $id = 0)
    {
        self::radioArray($isSmsVerify, 'is_sms_verify', 'IsSmsVerify', $id);
    }

    public static function isSmsNotify($isSmsNotify = [], $id = 0)
    {
        self::radioArray($isSmsNotify, 'is_sms_notify', 'IsSmsNotify', $id);
    }

    public static function itemType($itemType = [], $id = 0)
    {
        self::radioArray($itemType, 'item_type', 'ItemType', $id);
    }

    public static function isShowPrice($isShowPrice = [], $id = 0)
    {
        self::radioArray($isShowPrice, 'is_show_price', 'IsShowPrice', $id);
    }

    public static function isDistribution($isDistribution = [], $id = 0)
    {
        self::radioArray($isDistribution, 'is_distribution', 'IsDistribution', $id);
    }

    public static function isView($isView = [], $id = 0)
    {
        self::selectArray($isView, 'IsView', $id);
    }

    public static function orderDetailSort($detail = [])
    {
        $detailSortConfig = [];
        if (Config::get('order_ui.detail_sort')) {
            $detailSortConfig = Config::get('order_ui.detail_sort');
        } else {
            foreach ($detail as $key => $value) {
                $detailSortConfig[$key] = 1;
            }
        }
        $html = '<ul>';
        foreach ($detailSortConfig as $key => $value) {
            $html .= '<li><div class="check-box"><label><input type="checkbox" name="detail_selected[' . $key .
                ']" ' . ($value ? 'checked' : '') . '>' . $detail[$key] .
                '<input type="hidden" name="detail_sort[]" value="' . $key . '"></label></div></li>';
        }
        $html .= '</ul>';
        View::assign(['DetailSort' => $html]);
    }

    public static function orderList($list = [])
    {
        $listConfig = [];
        if (Config::get('order_ui.list')) {
            $listConfig = Config::get('order_ui.list');
        } else {
            foreach ($list as $key => $value) {
                $listConfig[$key][0] = $value[1];
                $listConfig[$key][1] = $value[2] ?? 1;
            }
        }
        $html = '<ul>';
        foreach ($listConfig as $key => $value) {
            $html .= '<li><div class="check-box"><label><input type="checkbox" name="list_selected[' . $key .
                ']" ' . ($value[1] ? 'checked' : '') . '>' . $list[$key][0] .
                '</label></div><input type="text" name="list_width[' . $key . ']" value="' . $value[0] .
                '" class="text"><input type="hidden" name="list_sort[]" value="' . $key . '"></li>';
        }
        $html .= '</ul>';
        View::assign(['List' => $html]);
    }

    public static function orderSearchSort($search = [])
    {
        $searchSortConfig = [];
        if (Config::get('order_ui.search_sort')) {
            $searchSortConfig = Config::get('order_ui.search_sort');
        } else {
            foreach ($search as $key => $value) {
                $searchSortConfig[$key] = 1;
            }
        }
        $html = '<ul>';
        foreach ($searchSortConfig as $key => $value) {
            $html .= '<li><div class="check-box"><label><input type="checkbox" name="search_selected[' . $key .
                ']" ' . ($value ? 'checked' : '') . '>' . $search[$key] .
                '<input type="hidden" name="search_sort[]" value="' . $key . '"></label></div></li>';
        }
        $html .= '</ul>';
        View::assign(['SearchSort' => $html]);
    }

    public static function orderOutputSort($output = [])
    {
        $orderOutput = [];
        if (Config::get('order_output')) {
            $orderOutput = Config::get('order_output');
        } else {
            foreach ($output as $key => $value) {
                $orderOutput[$key] = 1;
            }
        }
        $html = '<ul>';
        foreach ($orderOutput as $key => $value) {
            $html .= '<li><div class="check-box"><label><input type="checkbox" name="selected[' . $key . ']" ' .
                ($value ? 'checked' : '') . '>' . $output[$key] .
                '<input type="hidden" name="sort[]" value="' . $key . '"></label></div></li>';
        }
        $html .= '</ul>';
        View::assign(['OrderOutput' => $html]);
    }

    public static function orderIsCommissionRadio($isCommission = [], $id = 0)
    {
        self::radioArray($isCommission, 'is_commission', 'IsCommission', $id);
    }

    public static function orderSearch($search = [], $isCommission = [])
    {
        if (Config::get('order_ui.search', 1) == 0) {
            return '';
        }
        $data = [
            '<span>关 键 词：<input type="text" name="keyword" value="' . Request::get('keyword') .
                '" class="text"></span>',
            '<span>订购商品：<select name="product_id" lay-search><option value="0">不限</option>' .
                self::product(Request::get('product_id'), 2) . '</select></span>',
            '<span>成交单价：<div name="price1" value="' . Request::get('price1') .
                '" class="number"></div> ～ <div name="price2" value="' . Request::get('price2') .
                '" class="number"></div></span>',
            '<span>订购数量：<div name="count1" value="' . Request::get('count1') .
                '" class="number"></div> ～ <div name="count2" value="' . Request::get('count2') .
                '" class="number"></div></span>',
            '<span>成交总价：<div name="total1" value="' . Request::get('total1') .
                '" class="number"></div> ～ <div name="total2" value="' . Request::get('total2') .
                '" class="number"></div></span>',
            '<span>分销结算：<select name="is_commission" lay-search><option value="-1">不限</option>' .
                self::orderIsCommissionSelect($isCommission, Request::get('is_commission', -1)) . '</select></span>',
            '<span class="manager_id">管理员/分销商：<select name="manager_id" lay-search><option value="-1">不限</option>' .
                self::manager2(Request::get('manager_id', -1)) . '</select></span>',
            '<span>下单时间：<input type="text" name="date1" value="' . Request::get('date1') . '" class="text date"> ～ ' .
                '<input type="text" name="date2" value="' . Request::get('date2') . '" class="text date"></span>',
            '<span>支付方式：<select name="payment_id" lay-filter="payment_id" lay-search><option value="0">不限</option>' .
                self::orderPaymentSelect(Request::get('payment_id')) . '</select></span><span class="alipay_scene">' .
                '支付场景：<select name="alipay_scene" lay-search><option value="0">不限</option>' .
                self::alipayScene(Request::get('alipay_scene')) . '</select></span><span class="wechat_pay_scene">' .
                '支付场景：<select name="wechat_pay_scene" lay-search><option value="0">不限</option>' .
                self::wechatPayScene(Request::get('wechat_pay_scene')) . '</select></span>',
            '<span>支付时间：<input type="text" name="pay_date1" value="' . Request::get('pay_date1') .
                '" class="text date"> ～ <input type="text" name="pay_date2" value="' . Request::get('pay_date2') .
                '" class="text date"></span>',
            '<span>订单状态：<select name="order_state_id" lay-search><option value="0">不限</option>' .
                self::orderStateSelect(Request::get('order_state_id')) . '</select></span>',
            '<span>快递公司：<select name="express_id" lay-search><option value="0">不限</option>' .
            self::express(Request::get('express_id')) . '</select></span>',
            '<span>下单模板：<select name="template_id" lay-search><option value="0">不限</option>' .
            self::template2(Request::get('template_id')) . '</select></span>'
        ];
        $searchSort = [];
        if (Config::get('order_ui.search_sort')) {
            $searchSort = Config::get('order_ui.search_sort');
        } else {
            foreach ($search as $key => $value) {
                $searchSort[$key] = 1;
            }
        }
        $html = '<form method="get" action="" class="search layui-form">';
        foreach ($searchSort as $key => $value) {
            if ($value) {
                $html .= $data[$key];
            }
        }
        return $html . '<input type="submit" value="查询" class="button third"><p class="clear"></p></form>';
    }
}
