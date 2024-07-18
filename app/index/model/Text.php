<?php

namespace app\index\model;

use Exception;
use think\Model;

class Text extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('content')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询内容
    public function content($id = 0)
    {
        $one = $this->one($id);
        return $one ? $one['content'] : '';
    }

    //添加
    public function add($content = '')
    {
        return $this->insertGetId(['content' => $content]);
    }
}
