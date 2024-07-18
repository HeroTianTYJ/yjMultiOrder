<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class Brand extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map = [
                'where' => '`name` LIKE :name AND `is_view`=1',
                'value' => [
                    'name' => '%' . Request::get('keyword') . '%'
                ]
            ];
            if (Request::get('category_id')) {
                $map['where'] .= ' AND `category_id`=:category_id';
                $map['value']['category_id'] = Request::get('category_id');
            }
            return $this->field('id,name,logo,color,category_id')
                ->where($map['where'], $map['value'])
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
            return $this->field('name,color')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
