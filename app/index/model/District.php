<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class District extends Model
{
    //联动
    public function all()
    {
        try {
            return $this->field('id,name')
                ->where(['parent_id' => Request::post('parent_id')])
                ->order(['id' => 'ASC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
