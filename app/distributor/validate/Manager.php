<?php

namespace app\distributor\validate;

use app\common\validate\Base;

class Manager extends Base
{
    protected $rule = [
        'name' => 'require|max:20',
        'pass' => 'require',
        'repass' => 'require|confirm:pass',
        'email' => 'max:255',
        'bank' => 'max:30',
        'real_name' => 'max:20',
        'account' => 'max:50',
    ];
    protected $message = [
        'name' => '账号不得为空或大于20位！',
        'pass' => '密码不得为空！',
        'repass' => '两次输入的密码不相同！',
        'email' => '电子邮箱不得大于255位！',
        'bank' => '开户行不得大于30位！',
        'real_name' => '开户人姓名不得大于20位！',
        'account' => '开户账号不得大于50位！',
    ];
}
