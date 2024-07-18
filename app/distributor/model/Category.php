<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class Category extends Model
{
    //查询所有（主分类）
    public function all()
    {
        try {
            return $this->field('id,name,color')
                ->where(
                    '`name` LIKE :name AND `parent_id`=0 AND `is_view`=1',
                    ['name' => '%' . Request::get('keyword') . '%']
                )
                ->order(['sort' => 'ASC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('name,color,parent_id')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
