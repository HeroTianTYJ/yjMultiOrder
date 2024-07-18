<?php

namespace app\distributor\model;

use Exception;
use think\Model;

class Template extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('name')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
