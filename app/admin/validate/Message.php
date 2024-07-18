<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Message extends Base
{
    protected $rule = [
        'name' => 'require|max:20',
        'tel' => 'require|regex:/^[\d\-]{7,20}$/',
        'email' => 'email',
        'content' => 'require|max:65535',
        'reply' => 'max:65535',
    ];
    protected $message = [
        'name' => '姓名不得为空或大于20位！',
        'tel' => '联系电话必须是数字和-号，且不得小于7位或大于20位！',
        'email' => '电子邮箱格式不合法！',
        'content' => '留言内容不得为空或大于65535位！',
        'reply' => '回复内容不得大于65535位！',
    ];
}
