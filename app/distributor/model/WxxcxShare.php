<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class WxxcxShare extends Model
{
    //查询一条
    public function one($type = 0, $pageId = 0)
    {
        try {
            return $this->field('id,url_suffix')
                ->where([
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                    'wxxcx_id' => Request::post('id'),
                    'type' => $type,
                    'page_id' => $pageId
                ])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add($type = 0, $pageId = 0, $urlSuffix = '')
    {
        return $this->insertGetId([
            'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
            'wxxcx_id' => Request::post('id'),
            'type' => $type,
            'page_id' => $pageId,
            'url_suffix' => $urlSuffix
        ]);
    }
}
