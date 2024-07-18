<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class Wxxcx extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map['where'] = '`name` LIKE :name';
            $map['value']['name'] = '%' . Request::get('keyword') . '%';
            if (Request::get('shop_id')) {
                $map['where'] .= ' AND `shop_id`=:shop_id';
                $map['value']['shop_id'] = Request::get('shop_id');
            }
            return $this->field('id,name,shop_id')
                ->where($map['where'], $map['value'])
                ->order(['id' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2()
    {
        try {
            return $this->field('id,name')->order(['id' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('name,app_id,app_secret')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
