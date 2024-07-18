<?php

namespace app\admin\model;

use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class VisitWxxcx extends Model
{
    //查询总记录
    public function totalCount()
    {
        try {
            return $this->where('date1', '>', strtotime(date('Y-m-d') . ' 00:00:00'))->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            $map['where'] = '`ip` LIKE :ip';
            $map['value']['ip'] = '%' . Request::get('keyword') . '%';
            if (Request::get('manager_id')) {
                $map['where'] .= ' AND `manager_id`=:manager_id';
                $map['value']['manager_id'] = Request::get('manager_id');
            }
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
            return $this->field('id,ip,manager_id,wxxcx_id,type,page_id,scene_id,count,date1,date2')
                ->where($map['where'], $map['value'])
                ->order(['date2' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2()
    {
        try {
            return $this->field('ip,manager_id,wxxcx_id,type,page_id,scene_id,count,date1,date2')
                ->order(['date2' => 'DESC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
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
