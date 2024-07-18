<?php

namespace app\distributor\validate;

use app\common\validate\Base;

class Balance extends Base
{
    protected $rule = [
        'price' => 'require|price',
    ];
    protected $message = [
        'price' => '提现金额必须是数字！',
    ];
}
