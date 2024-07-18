<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class Category extends Model
{
    //查询所有（不分页）
    public function all($parentId = 0)
    {
        try {
            return $this->field('id,name,color,is_default')
                ->where(['parent_id' => $parentId, 'is_view' => 1])
                ->order(['sort' => 'ASC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0, $isView = 1)
    {
        try {
            return $this->field('id,name,parent_id')
                ->where($isView ? ['id' => Request::param('id'), 'parent_id' => 0, 'is_view' => 1] : ['id' => $id])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function one2()
    {
        try {
            return $this->field('id,name')->where(['is_default' => 1, 'is_view' => 1])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function one3()
    {
        try {
            return $this->field('id,name')->where(['is_view' => 1])->order(['sort' => 'ASC'])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
