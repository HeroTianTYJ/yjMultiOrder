<?php

namespace app\admin\model;

use Exception;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class WxxcxShare extends Model
{
    //查询所有
    public function all($type = 0)
    {
        try {
            return $this->field('id')
                ->where(['type' => $type])
                ->where('page_id', 'IN', Request::post('id') ?: Request::post('ids'))
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function all2()
    {
        try {
            return $this->field('id')
                ->where('wxxcx_id', 'IN', Request::post('id') ?: Request::post('ids'))
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function all3()
    {
        try {
            return $this->field('id')
                ->where('manager_id', 'IN', Request::post('id') ?: Request::post('ids'))
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //删除
    public function remove($type = 0)
    {
        try {
            $affectedRows = $this->where(['type' => $type])
                ->where('page_id', 'IN', Request::post('id') ?: Request::post('ids'))
                ->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function remove2()
    {
        try {
            $affectedRows = $this->where('wxxcx_id', 'IN', Request::post('id') ?: Request::post('ids'))->delete();
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
            $affectedRows = $this->where('manager_id', 'IN', Request::post('id') ?: Request::post('ids'))->delete();
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
