<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Session;
use think\Model;

class Click extends Model
{
    //查询一条
    public function one($type = 0, $pageId = 0, $wxxcxId = 0)
    {
        try {
            return $this->field('click')
                ->where([
                    'type' => $type,
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id'),
                    'wxxcx_id' => $wxxcxId,
                    'page_id' => $pageId
                ])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
