<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Brand extends Base
{
    protected $rule = [
        'name' => 'require|max:20',
        'color' => 'max:20',
        'logo' => 'require|max:25',
        'category_id' => 'require',
    ];
    protected $message = [
        'name' => '品牌名称不得为空或大于20位！',
        'color' => '品牌颜色不得大于20位！',
        'logo.require' => '请插入品牌logo！',
        'logo.max' => '品牌logo不得大于25位！',
        'category_id' => '请先在品牌分类模块中添加一个二级分类！',
    ];
}
