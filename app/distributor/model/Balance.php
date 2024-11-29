<?php

namespace app\distributor\model;

use app\distributor\validate\Balance as validate;
use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Balance extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map = [
                'where' => '`manager_id`=:manager_id',
                'value' => [
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                ]
            ];
            if (Request::get('type', -1) == 0) {
                $map['where'] .= ' AND `price`>0';
            } elseif (Request::get('type') == 1) {
                $map['where'] .= ' AND `price`<0';
            }
            return $this->field('id,price,date')
                ->where($map['where'], $map['value'])
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //计算统计数据
    public function sum()
    {
        try {
            return $this->field('SUM(CASE WHEN `price`>0 THEN `price` END) `sum1`,' .
                'ABS(SUM(CASE WHEN `price`<0 THEN `price` END)) `sum2`,SUM(`price`) `sum3`')
                ->where([
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                ])
                ->select()
                ->toArray()[0];
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add($price)
    {
        return $this->insertGetId([
            'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
            'price' => $price,
            'date' => time()
        ]);
    }
    public function add2()
    {
        $data = [
            'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
            'price' => Request::post('price'),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (Request::post('price') > $this->sum()['sum3']) {
                return '您输入的金额大于您的可提现金额！';
            }
            $data['price'] = -Request::post('price');
            return $this->insertGetId($data);
        } else {
            return implode($validate->getError());
        }
    }
}
