<?php

namespace app\distributor\model;

use Exception;
use think\Model;

class Bill extends Model
{
    //查询最新一条
    public function newer()
    {
        try {
            return $this->field('all_in,all_out,all_add')
                ->order(['date' => 'DESC'])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add(
        $managerId = 0,
        $name = '',
        $inPrice = 0,
        $inCount = 0,
        $inPriceOut = 0,
        $inCountOut = 0
    ) {
        $data = [
            'manager_id' => $managerId ?: 1,
            'name' => $name,
            'bill_sort_id' => 1,
            'in_price' => $inPrice,
            'in_count' => $inCount,
            'in_price_out' => $inPriceOut,
            'in_count_out' => $inCountOut,
            'date' => time()
        ];
        $newer = $this->newer();
        $data['all_in'] = $data['all_add'] = ($newer ? $newer['all_in'] : 0) + $data['in_price'] * $data['in_count'] -
            $data['in_price_out'] * $data['in_count_out'];
        return $this->insertGetId($data);
    }
}
