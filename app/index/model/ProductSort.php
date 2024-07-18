<?php

namespace app\index\model;

use Exception;
use think\Model;

class ProductSort extends Model
{
    //查询所有（不分页）
    public function all($ids = 0)
    {
        try {
            $all = $this->field('id,name,color')->order(['sort' => 'ASC']);
            return $ids ? $all->where('id', 'IN', $ids)->select()->toArray() : $all->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
