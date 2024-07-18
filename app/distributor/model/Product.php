<?php

namespace app\distributor\model;

use Exception;
use think\Model;

class Product extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('name,price,price2,low_price,high_price')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
