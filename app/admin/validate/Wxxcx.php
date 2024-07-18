<?php

namespace app\admin\validate;

use think\Validate;

class Wxxcx extends Validate
{
    protected $rule = [
        'name' => 'require|max:30',
        'submit_key' => 'require|min:20',
        'app_id' => 'length:18',
        'app_secret' => 'length:32',
        'pay_mchid' => 'length:10',
        'pay_key' => 'length:32',
    ];
    protected $message = [
        'name' => '小程序名称不得为空或大于30位！',
        'submit_key' => '交互密钥不得小于20位！',
        'app_id' => 'AppID必须为18位！',
        'app_secret' => 'AppSecret必须为32位！',
        'pay_mchid' => '支付MCHID必须为10位！',
        'pay_key' => '支付KEY必须为32位！',
    ];
}
