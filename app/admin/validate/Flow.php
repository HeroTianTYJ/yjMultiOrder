<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Flow extends Base
{
    protected $rule = [
        'name' => 'require|max:50',
        'price' => 'require|price',
        'note' => 'max:65535',
    ];
    protected $message = [
        'name' => '流动名称不得为空或大于50位！',
        'price' => '流动金额必须是数字！',
        'note' => '备注不得大于65535位！',
    ];
}
