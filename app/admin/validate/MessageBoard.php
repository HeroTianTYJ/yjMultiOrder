<?php

namespace app\admin\validate;

use app\common\validate\Base;

class MessageBoard extends Base
{
    protected $rule = [
        'name' => 'require|max:20',
        'field' => 'require|checkbox:0,1,2,3',
        'time' => 'number',
        'page' => 'number',
    ];
    protected $message = [
        'name' => '留言板名称不得为空或大于20位！',
        'field.require' => '请勾选留言字段！',
        'field.checkbox' => '留言字段设置不合法！',
        'time' => '留言间隔必须是数字！',
        'page' => '每页留言数必须是数字！',
    ];
}
