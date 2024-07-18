<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Balance extends Base
{
    protected $rule = [
        'price' => 'require|price',
    ];
    protected $message = [
        'price' => '结算金额必须是数字！',
    ];
}
