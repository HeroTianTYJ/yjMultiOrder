<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class Item extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map['where'] = '(';
            foreach (['name', 'price1', 'price2', 'sale'] as $value) {
                $map['where'] .= '`' . $value . '` LIKE :' . $value . ' OR ';
                $map['value'][$value] = '%' . Request::get('keyword') . '%';
            }
            $map['where'] = substr($map['where'], 0, -4) . ') AND `is_view`=1 AND `is_distribution`=1';
            if (Request::get('brand_id')) {
                $map['where'] .= ' AND `brand_id`=:brand_id';
                $map['value']['brand_id'] = Request::get('brand_id');
            }
            return $this->field('id,name,brand_id,price1,price2,sale')
                ->where($map['where'], $map['value'])
                ->order(['date' => 'DESC'])
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
            return $this->field('name')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
