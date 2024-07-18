<?php

namespace app\index\model;

use Exception;
use think\Model;

class CategoryPage extends Model
{
    //查询一条
    public function one()
    {
        try {
            return $this->field('name,title,keyword,description,width,left_width,bg_color,copyright,code_type,' .
                'text_id_code,text_id_nav,icon,share_title,share_pic,share_desc')
                ->where(['id' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
