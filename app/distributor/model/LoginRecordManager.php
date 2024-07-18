<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class LoginRecordManager extends Model
{
    //查询总记录
    public function totalCount()
    {
        try {
            return $this->where([
                'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
            ])
                ->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            return $this->field('id,ip,date')
                ->where('`ip` LIKE :ip AND `manager_id`=:manager_id', [
                    'ip' => '%' . Request::get('keyword') . '%',
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                ])
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one()
    {
        try {
            return $this->field('ip,date')
                   ->where([
                       'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                   ])
                   ->order(['date' => 'DESC'])
                   ->limit(1, 1)
                   ->select()[0];
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add($managerId)
    {
        return $this->insertGetId(['manager_id' => $managerId, 'ip' => getUserIp(), 'date' => time()]);
    }
}
