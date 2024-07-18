<?php

namespace app\admin\model;

use Exception;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Click extends Model
{
    //查询一条
    public function one($type = 0, $managerId = 0, $pageId = 0, $wxxcxId = 0)
    {
        try {
            return $this->field('click')
                ->where(['type' => $type, 'manager_id' => $managerId, 'wxxcx_id' => $wxxcxId, 'page_id' => $pageId])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //删除
    public function remove($types = [])
    {
        try {
            $affectedRows = $this->where(['page_id' => Request::get('id')])->where('type', 'IN', $types)->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function remove2($types = [])
    {
        try {
            $affectedRows = $this->where(['wxxcx_id' => Request::get('id')])->where('type', 'IN', $types)->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function remove3()
    {
        try {
            $affectedRows = $this->where(['manager_id' => Request::get('id')])->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
