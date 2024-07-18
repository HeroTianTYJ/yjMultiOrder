<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Product extends Base
{
    protected $rule = [
        'name' => 'require|max:30',
        'product_sort_id' => 'require',
        'price' => 'require|price',
        'price2' => 'price',
        'commission' => 'price|between:0,100',
        'color' => 'max:20',
        'email' => 'max:255',
        'attr' => 'max:65535',
        'low_price' => 'price',
        'high_price' => 'price',
        'own_price' => 'max:65535',
    ];
    protected $message = [
        'name' => '商品名称不得为空或大于30位！',
        'product_sort_id' => '请先在商品分类模块中添加一个分类！',
        'price' => '商品价格必须是数字！',
        'price2' => '成本价必须是数字！',
        'commission' => '分销比例必须是0到100的数字！',
        'color' => '商品颜色不得大于20位！',
        'email' => '管理邮箱不得大于255位！',
        'attr' => '销售属性过多！',
        'low_price' => '价格区间必须是数字！',
        'high_price' => '价格区间必须是数字！',
        'own_price' => '独立价格过多！',
    ];
}
