<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Captcha extends Base
{
    protected $rule = [
        'name' => 'require',
        'type' => 'require|between:0,2',
        'length' => 'require|number',
        'fontSize' => 'require|number',
        'imageW' => 'require|number',
        'imageH' => 'require|number',
        'bgR' => 'require|number|between:0,255',
        'bgG' => 'require|number|between:0,255',
        'bgB' => 'require|number|between:0,255',
        'useImgBg' => 'require|between:0,1',
        'useCurve' => 'require|between:0,1',
        'useNoise' => 'require|between:0,1',
    ];
    protected $message = [
        'name' => '验证码名称不得为空！',
        'type' => '验证码类型不合法！',
        'length' => '验证码位数必须是数字！',
        'fontSize' => '验证码字号必须是数字！',
        'imageW' => '验证码宽度必须是数字！',
        'imageH' => '验证码高度必须是数字！',
        'bgR' => '背景颜色R必须是0到255之间的数字！',
        'bgG' => '背景颜色G必须是0到255之间的数字！',
        'bgB' => '背景颜色B必须是0到255之间的数字！',
        'useImgBg' => '背景图设置不合法！',
        'useCurve' => '混淆曲线设置不合法！',
        'useNoise' => '杂点设置不合法！',
    ];
}
