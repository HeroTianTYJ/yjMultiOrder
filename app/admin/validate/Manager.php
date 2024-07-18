<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Manager extends Base
{
    protected $rule = [
        'name' => 'require|max:20',
        'pass' => 'require',
        'repass' => 'require|confirm:pass',
        'admin_mail' => 'require',
        'email' => 'max:255',
        'level' => 'require|between:1,3',
        'is_activation' => 'require|between:0,1',
        'order_permit' => 'require|between:1,3',
        'bill_permit' => 'require|between:1,2',
        'bank' => 'max:30',
        'real_name' => 'max:20',
        'account' => 'max:50',
    ];
    protected $message = [
        'name' => '账号不得为空或大于20位！',
        'pass' => '密码不得为空！',
        'repass' => '两次输入的密码不相同！',
        'admin_mail' => '管理员邮箱不得为空！',
        'email' => '电子邮箱不得大于255位！',
        'level' => '身份设置不合法！',
        'is_activation' => '激活设置不合法！',
        'order_permit' => '订单权限设置不合法！',
        'bill_permit' => '账单权限设置不合法！',
        'bank' => '开户行不得大于30位！',
        'real_name' => '开户人姓名不得大于20位！',
        'account' => '开户账号不得大于50位！'
    ];
}
