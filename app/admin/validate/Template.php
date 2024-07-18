<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Template extends Base
{
    protected $rule = [
        'type' => 'require|between:0,1',
        'name' => 'require|max:20',
        'template' => 'require|between:0,4',
        'product_type' => 'require|between:0,1',
        'product_sort_ids' => 'max:255',
        'product_ids' => 'max:255',
        'product_view_type' => 'require|between:0,1',
        'field_ids' => 'checkbox:1,2,3,4,5,6,7,8,9',
        'payment_ids' => 'checkbox:1,2,3',
        'payment_default' => 'require|between:1,3',
        'is_show_search' => 'require|between:0,1',
        'is_show_send' => 'require|between:0,1',
        'is_sms_verify' => 'require|between:0,1',
        'is_sms_notify' => 'require|between:0,1',
        'success' => 'require|max:255',
        'success2' => 'require|max:255',
        'often' => 'require|max:255',
    ];
    protected $message = [
        'type' => '模板类型设置不合法！',
        'name' => '模板名称不得为空或大于20位！',
        'template' => '所用模板设置不合法！',
        'product_type' => '商品分类设置不合法！',
        'product_sort_ids' => '选择的商品分类过多！',
        'product_ids' => '选择的商品过多！',
        'product_view_type' => '商品显示设置不合法！',
        'field_ids' => '下单字段设置不合法！',
        'payment_ids' => '支付方式设置不合法！',
        'payment_default' => '默认支付方式设置不合法！',
        'is_show_search' => '订单查询设置不合法！',
        'is_show_send' => '发货通知设置不合法！',
        'is_sms_verify' => '短信设置不合法！',
        'is_sms_notify' => '短信通知设置不合法！',
        'success' => '提交成功的提示信息不得为空或大于255位！',
        'success2' => '支付成功的提示信息不得为空或大于255位！',
        'often' => '频繁提交的提示信息不得为空或大于255位！',
    ];
}
