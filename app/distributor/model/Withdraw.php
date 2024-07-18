<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Withdraw extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map = [
                'where' => '`price` LIKE :price AND `manager_id`=:manager_id',
                'value' => [
                    'price' => '%' . Request::get('keyword') . '%',
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                ]
            ];
            if (Request::get('state', -1) != -1) {
                $map['where'] .= ' AND `state`=:state';
                $map['value']['state'] = Request::get('state');
            }
            return $this->field('id,price,state,date')
                ->where($map['where'], $map['value'])
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add()
    {
        return $this->insertGetId([
            'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
            'price' => Request::post('price'),
            'date' => time()
        ]);
    }
}
