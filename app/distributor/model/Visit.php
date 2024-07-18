<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Visit extends Model
{
    //查询总记录
    public function totalCount()
    {
        try {
            return $this->where([
                'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
            ])
                ->where('date1', '>', strtotime(date('Y-m-d') . ' 00:00:00'))
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
            return $this->field('ip,manager_id,url,count,date1,date2')
                ->where('(`ip` LIKE :ip OR `url` LIKE :url) AND `manager_id`=:manager_id', [
                    'ip' => '%' . Request::get('keyword') . '%',
                    'url' => '%' . Request::get('keyword') . '%',
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                ])
                ->order(['date2' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
