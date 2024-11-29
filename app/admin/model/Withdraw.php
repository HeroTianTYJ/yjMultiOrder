<?php

namespace app\admin\model;

use app\admin\validate\Withdraw as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Withdraw extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map['where'] = '`price` LIKE :keyword';
            $map['value']['keyword'] = '%' . Request::get('keyword') . '%';
            if (Request::get('manager_id')) {
                $map['where'] .= ' AND `manager_id`=:manager_id';
                $map['value']['manager_id'] = Request::get('manager_id');
            }
            if (Request::get('state', -1) != -1) {
                $map['where'] .= ' AND `state`=:state';
                $map['value']['state'] = Request::get('state');
            }
            return $this->field('id,manager_id,price,state,date')
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
            return $this->field('id,manager_id,price,state,date')->where(['id' => $id ?: Request::post('id')])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //修改
    public function modify()
    {
        $data = [
            'price' => Request::post('price'),
            'state' => Request::post('state')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return implode($validate->getError());
        }
    }

    //删除
    public function remove()
    {
        try {
            $affectedRows = $this->where('id', 'IN', Request::post('id') ?: Request::post('ids'))->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
