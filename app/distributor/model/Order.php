<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Order extends Model
{
    //按订单状态查询总记录
    public function totalCount($orderStateId = 0)
    {
        try {
            $map = [
                'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                'is_recycle' => 0
            ];
            if ($orderStateId) {
                $map['order_state_id'] = $orderStateId;
            }
            return $this->where($map)->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            return $this->field('id,order_id,template_id,product_id,attr,price,count,payment_id,order_state_id,' .
                'express_id,express_number,commission,is_commission,date')
                ->where($this->map()['where'], $this->map()['value'])
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //计算可结算订单
    public function balance()
    {
        try {
            $map = [
                'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                'order_state_id' => 4,
                'is_commission' => 0,
                'is_recycle' => 0
            ];
            $one = $this->field('COUNT(`id`) `count`,SUM(`commission`*`count`) `sum`')->where($map)->find();
            $all = $this->field('id')->where($map)->select()->toArray();
            return [$one, $all];
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //按自定义时间统计
    public function diyTime($time1 = 0, $time2 = 0)
    {
        try {
            $map = $this->map();
            if ($time1 && $time2) {
                $map['where'] .= ' AND `date`>=:date3 AND `date`<=:date4';
                $map['value']['date3'] = strtotime($time1 . ' 00:00:00') . '';
                $map['value']['date4'] = strtotime($time2 . ' 23:59:59') . '';
            }
            return $this->field('COUNT(CASE WHEN `order_state_id`=1 THEN `id` END) `count1`,' .
                'SUM(CASE WHEN `order_state_id`=1 THEN `price`*`count` ELSE 0 END) `sum1`,' .
                'COUNT(CASE WHEN `order_state_id`=2 THEN `id` END) `count2`,' .
                'SUM(CASE WHEN `order_state_id`=2 THEN `price`*`count` ELSE 0 END) `sum2`,' .
                'COUNT(CASE WHEN `order_state_id`=3 THEN `id` END) `count3`,' .
                'SUM(CASE WHEN `order_state_id`=3 THEN `price`*`count` ELSE 0 END) `sum3`,' .
                'COUNT(CASE WHEN `order_state_id`=4 THEN `id` END) `count4`,' .
                'SUM(CASE WHEN `order_state_id`=4 THEN `price`*`count` ELSE 0 END) `sum4`,' .
                'COUNT(CASE WHEN `order_state_id`=5 THEN `id` END) `count5`,' .
                'SUM(CASE WHEN `order_state_id`=5 THEN `price`*`count` ELSE 0 END) `sum5`,' .
                'COUNT(CASE WHEN `order_state_id`=6 THEN `id` END) `count6`,' .
                'SUM(CASE WHEN `order_state_id`=6 THEN `price`*`count` ELSE 0 END) `sum6`')
                ->where($map['where'], $map['value'])
                ->select()
                ->toArray()[0];
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //按天、月、年统计
    public function dayMonthYear($time, $paginate = true)
    {
        try {
            switch (Request::get('order')) {
                case 1:
                    $order = 'count1';
                    break;
                case 2:
                    $order = 'count2';
                    break;
                case 3:
                    $order = 'count3';
                    break;
                case 4:
                    $order = 'count4';
                    break;
                case 5:
                    $order = 'count5';
                    break;
                case 6:
                    $order = 'count6';
                    break;
                case 7:
                    $order = 'sum1';
                    break;
                case 8:
                    $order = 'sum2';
                    break;
                case 9:
                    $order = 'sum3';
                    break;
                case 10:
                    $order = 'sum4';
                    break;
                case 11:
                    $order = 'sum5';
                    break;
                case 12:
                    $order = 'sum6';
                    break;
                default:
                    $order = 'time';
            }
            $all = $this->field('COUNT(CASE WHEN `order_state_id`=1 THEN `id` END) `count1`,' .
                'SUM(CASE WHEN `order_state_id`=1 THEN `price`*`count` ELSE 0 END) `sum1`,' .
                'COUNT(CASE WHEN `order_state_id`=2 THEN `id` END) `count2`,' .
                'SUM(CASE WHEN `order_state_id`=2 THEN `price`*`count` ELSE 0 END) `sum2`,' .
                'COUNT(CASE WHEN `order_state_id`=3 THEN `id` END) `count3`,' .
                'SUM(CASE WHEN `order_state_id`=3 THEN `price`*`count` ELSE 0 END) `sum3`,' .
                'COUNT(CASE WHEN `order_state_id`=4 THEN `id` END) `count4`,' .
                'SUM(CASE WHEN `order_state_id`=4 THEN `price`*`count` ELSE 0 END) `sum4`,' .
                'COUNT(CASE WHEN `order_state_id`=5 THEN `id` END) `count5`,' .
                'SUM(CASE WHEN `order_state_id`=5 THEN `price`*`count` ELSE 0 END) `sum5`,' .
                'COUNT(CASE WHEN `order_state_id`=6 THEN `id` END) `count6`,' .
                'SUM(CASE WHEN `order_state_id`=6 THEN `price`*`count` ELSE 0 END) `sum6`,' .
                'FROM_UNIXTIME(`date`,\'' . $time . '\') `time`')
                ->group('FROM_UNIXTIME(`date`,\'' . $time . '\')')
                ->where($this->map()['where'], $this->map()['value'])
                ->order([$order => 'DESC']);
            return $paginate ? $all->paginate(Config::get('app.page_size')) : $all->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,order_id,template_id,product_id,attr,price,count,name,tel,province,city,county,' .
                'town,address,note,ip,payment_id,pay_id,pay_scene,pay_date,order_state_id,express_id,' .
                'express_number,commission,is_commission,date')
                ->where([
                    'id' => $id ?: Request::post('id'),
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                    'is_recycle' => 0
                ])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询最老一条
    public function older()
    {
        try {
            return $this->field('date')
                ->where([
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                    'is_recycle' => 0
                ])
                ->order(['date' => 'ASC'])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询最新一条
    public function newer()
    {
        try {
            return $this->field('date')
                ->where([
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                    'is_recycle' => 0
                ])
                ->order(['date' => 'DESC'])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //修改结算状态
    public function modify($ids = '')
    {
        return $this->where('id', 'IN', $ids)->where('is_commission', 0)->update(['is_commission' => 1]);
    }

    //高级搜索
    private function map()
    {
        $map['where'] = '1=1';
        $map['value'] = [];
        if (Request::get('keyword')) {
            $map['where'] .= ' AND (';
            foreach (['order_id', 'attr', 'express_number'] as $value) {
                $map['where'] .= '`' . $value . '` LIKE :' . $value . ' OR ';
                $map['value'][$value] = '%' . Request::get('keyword') . '%';
            }
            $map['where'] = substr($map['where'], 0, -4) . ')';
        }
        if (Request::get('user_id', -1) != -1) {
            $map['where'] .= ' AND `user_id`=:user_id';
            $map['value']['user_id'] = Request::get('user_id');
        }
        if (Request::get('product_id')) {
            $map['where'] .= ' AND `product_id`=:product_id';
            $map['value']['product_id'] = Request::get('product_id');
        }
        if (Request::get('payment_id')) {
            $map['where'] .= ' AND `payment_id`=:payment_id';
            $map['value']['payment_id'] = Request::get('payment_id');
        }
        if (Request::get('order_state_id')) {
            $map['where'] .= ' AND `order_state_id`=:order_state_id';
            $map['value']['order_state_id'] = Request::get('order_state_id');
        }
        if (Request::get('express_id')) {
            $map['where'] .= ' AND `express_id`=:express_id';
            $map['value']['express_id'] = Request::get('express_id');
        }
        if (Request::get('price1')) {
            $map['where'] .= ' AND `price`>=:price1';
            $map['value']['price1'] = Request::get('price1');
        }
        if (Request::get('price2')) {
            $map['where'] .= ' AND `price`<=:price2';
            $map['value']['price2'] = Request::get('price2');
        }
        if (Request::get('count1')) {
            $map['where'] .= ' AND `count`>=:count1';
            $map['value']['count1'] = Request::get('count1');
        }
        if (Request::get('count2')) {
            $map['where'] .= ' AND `count`<=:count2';
            $map['value']['count2'] = Request::get('count2');
        }
        if (Request::get('total1')) {
            $map['where'] .= ' AND `price`*`count`>=:total1';
            $map['value']['total1'] = Request::get('total1');
        }
        if (Request::get('total2')) {
            $map['where'] .= ' AND `price`*`count`<=:total2';
            $map['value']['total2'] = Request::get('total2');
        }
        if (Request::get('date1')) {
            $map['where'] .= ' AND `date`>=:date1';
            $map['value']['date1'] = strtotime(Request::get('date1') . ' 00:00:00');
        }
        if (Request::get('date2')) {
            $map['where'] .= ' AND `date`<=:date2';
            $map['value']['date2'] = strtotime(Request::get('date2') . ' 23:59:59');
        }
        if (Request::get('is_commission', -1) != -1) {
            $map['where'] .= ' AND `is_commission`=:is_commission';
            $map['value']['is_commission'] = Request::get('is_commission');
        }
        $map['where'] .= ' AND `is_recycle`=0 AND `manager_id`=' .
            Session::get(Config::get('system.session_key_distributor') . '.manage_info.id');
        return $map;
    }
}
