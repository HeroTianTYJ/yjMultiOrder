<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class Manager extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('name,email,level')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function one2($distributorCode = '')
    {
        try {
            return $this->field('id,level')
                ->where(['distributor_code' => $distributorCode ?: Request::get('code')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
