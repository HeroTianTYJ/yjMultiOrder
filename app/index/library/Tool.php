<?php

namespace app\index\library;

use app\index\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;

class Tool
{
    public static function click($pageId = 0, $type = '')
    {
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.' . $type), $pageId);
        $clickOne ? $Click->modify($clickOne['id']) : $Click->add(Config::get('page.' . $type), $pageId);

        $distributorId = Session::get(Config::get('system.session_key_index') . '.distributor_id');
        if ($distributorId) {
            $clickOne2 = $Click->one(Config::get('page.' . $type), $pageId, $distributorId);
            $clickOne2 ?
                $Click->modify($clickOne2['id']) :
                $Click->add(Config::get('page.' . $type), $pageId, $distributorId);
        }
        if (strstr($type, 'wxxcx')) {
            $clickOne = $Click->one(Config::get('page.' . $type), $pageId, 0, Request::get('wxxcx_id'));
            $clickOne ?
                $Click->modify($clickOne['id']) :
                $Click->add(Config::get('page.' . $type), $pageId, 0, Request::get('wxxcx_id'));
            if ($distributorId) {
                $clickOne2 = $Click->one(
                    Config::get('page.' . $type),
                    $pageId,
                    $distributorId,
                    Request::get('wxxcx_id')
                );
                $clickOne2 ?
                    $Click->modify($clickOne2['id']) :
                    $Click->add(Config::get('page.' . $type), $pageId, $distributorId, Request::get('wxxcx_id'));
            }
        }
    }
}
