<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Bill extends Base
{
    protected $rule = [
        'name' => 'require|max:50',
        'bill_sort_id' => 'require',
        'in_price' => 'price',
        'in_count' => 'number',
        'in_price_out' => 'price',
        'in_count_out' => 'number',
        'out_price' => 'price',
        'out_count' => 'number',
        'out_price_in' => 'price',
        'out_count_in' => 'number',
        'note' => 'max:65535',
    ];
    protected $message = [
        'name' => '账单名称不得为空或大于50位！',
        'bill_sort_id' => '请先在账单分类模块中添加一个分类！',
        'in_price' => '收入单价必须是数字！',
        'in_count' => '收入数量必须是数字！',
        'in_price_out' => '成本/退款单价必须是数字！',
        'in_count_out' => '成本/退款数量必须是数字！',
        'out_price' => '支出单价必须是数字！',
        'out_count' => '支出数量必须是数字！',
        'out_price_in' => '退款单价必须是数字！',
        'out_count_in' => '退款数量必须是数字！',
        'note' => '备注不得大于65535位！',
    ];
}
