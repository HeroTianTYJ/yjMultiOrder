<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class MessageBoard extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,field,captcha_id,time,page')->where(['id' => $id ?: Request::get('id')])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
