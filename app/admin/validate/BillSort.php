<?php

namespace app\admin\validate;

use app\common\validate\Base;

class BillSort extends Base
{
    protected $rule = [
        'name' => 'require|max:20',
        'type' => 'require|between:0,1',
    ];
    protected $message = [
        'name' => '分类名称不得为空或大于20位！',
        'type' => '分类类型不合法！',
    ];
}
