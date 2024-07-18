<?php

namespace app\index\model;

use Exception;
use think\Model;

class Click extends Model
{
    //查询一条
    public function one($type = 0, $pageId = 0, $managerId = 0, $wxxcxId = 0)
    {
        try {
            return $this->field('id')
                ->where(['type' => $type, 'manager_id' => $managerId, 'wxxcx_id' => $wxxcxId, 'page_id' => $pageId])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add($type = 0, $pageId = 0, $managerId = 0, $wxxcxId = 0)
    {
        return $this->insertGetId([
            'type' => $type,
            'manager_id' => $managerId,
            'wxxcx_id' => $wxxcxId,
            'page_id' => $pageId,
            'click' => 1
        ]);
    }

    //修改
    public function modify($id)
    {
        return $this->where(['id' => $id])->inc('click')->update();
    }
}
