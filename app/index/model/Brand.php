<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class Brand extends Model
{
    //查询所有
    public function all($categoryId = 0)
    {
        try {
            return $this->field('id,name,color,logo')
                ->where(['category_id' => $categoryId, 'is_view' => 1])
                ->order(['sort' => 'ASC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,logo,category_id')
                ->where(['id' => $id ?: Request::param('id'), 'is_view' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
