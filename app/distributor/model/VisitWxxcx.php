<?php

namespace app\distributor\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class VisitWxxcx extends Model
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
            $map = [
                'where' => '`ip` LIKE :ip AND `manager_id`=:manager_id',
                'value' => [
                    'ip' => '%' . Request::get('keyword') . '%',
                    'manager_id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')
                ]
            ];
            if (Request::get('wxxcx_id')) {
                $map['where'] .= ' AND `wxxcx_id`=:wxxcx_id';
                $map['value']['wxxcx_id'] = Request::get('wxxcx_id');
            }
            if (Request::get('type', -1) == 0) {
                $map['where'] .= ' AND `type`=0';
                if (Request::get('lists_id')) {
                    $map['where'] .= ' AND `page_id`=:page_id';
                    $map['value']['page_id'] = Request::get('lists_id');
                }
            } elseif (Request::get('type') == 1) {
                $map['where'] .= ' AND `type`=1';
                if (Request::get('item_id')) {
                    $map['where'] .= ' AND `page_id`=:page_id';
                    $map['value']['page_id'] = Request::get('item_id');
                }
            } elseif (Request::get('type') == 2) {
                $map['where'] .= ' AND `type`=2';
                if (Request::get('category_id')) {
                    $map['where'] .= ' AND `page_id`=:page_id';
                    $map['value']['page_id'] = Request::get('category_id');
                }
            } elseif (Request::get('type') == 3) {
                $map['where'] .= ' AND `type`=3';
                if (Request::get('brand_id')) {
                    $map['where'] .= ' AND `page_id`=:page_id';
                    $map['value']['page_id'] = Request::get('brand_id');
                }
            }
            if (Request::get('wxxcx_scene_id')) {
                $map['where'] .= ' AND `scene_id`=:scene_id';
                $map['value']['scene_id'] = Request::get('wxxcx_scene_id');
            }
            return $this->field('id,ip,wxxcx_id,type,page_id,scene_id,count,date1,date2')
                ->where($map['where'], $map['value'])
                ->order(['date2' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
