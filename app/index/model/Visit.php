<?php

namespace app\index\model;

use Exception;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Visit extends Model
{
    //查询所有（不分页）
    public function all()
    {
        try {
            return $this->field('ip,manager_id,url,count,date1,date2')->order(['date2' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询昨天是否有记录
    public function yesterday()
    {
        try {
            return $this->field('id')->where('date1', '<', strtotime(date('Y-m-d') . ' 00:00:00'))->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one()
    {
        try {
            $managerId = 0;
            if (Request::post('manager_id')) {
                $managerOne = (new Manager())->one(Request::post('manager_id'));
                if ($managerOne && $managerOne['level'] == 3) {
                    $managerId = Request::post('manager_id');
                }
            }
            return $this->field('id')
                ->where([
                    'ip' => getUserIp(),
                    'manager_id' => $managerId,
                    'url' => Request::post('url', '', null)
                ])
                ->where('date1', '>=', strtotime(date('Y-m-d') . ' 00:00:00'))
                ->where('date2', '<=', strtotime(date('Y-m-d') . ' 23:59:59'))
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add()
    {
        $managerId = 0;
        if (Request::post('manager_id')) {
            $managerOne = (new Manager())->one(Request::post('manager_id'));
            if ($managerOne && $managerOne['level'] == 3) {
                $managerId = Request::post('manager_id');
            }
        }
        return $this->insertGetId([
            'ip' => getUserIp(),
            'manager_id' => $managerId,
            'url' => Request::post('url', '', null),
            'count' => 1,
            'date1' => time(),
            'date2' => time()
        ]);
    }

    //修改
    public function modify($id)
    {
        $this->where(['id' => $id])->inc('count')->update();
        return $this->where(['id' => $id])->update(['date2' => time()]);
    }

    //清空表
    public function truncate()
    {
        try {
            return Db::execute('TRUNCATE `' . $this->getTable() . '`');
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
