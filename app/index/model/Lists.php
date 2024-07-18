<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class Lists extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,module,title,keyword,description,width,bg_color,copyright,code_type,' .
                'text_id_code,text_id_item_ids,page,text_id_banner,text_id_nav,icon,share_title,share_pic,share_desc')
                ->where(['id' => $id ?: Request::param('id'), 'is_view' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条（按页面地址）
    public function one2($module = '')
    {
        try {
            return $this->field('id,name,module,title,keyword,description,width,bg_color,copyright,code_type,' .
                'text_id_code,text_id_item_ids,page,text_id_banner,text_id_nav,icon,share_title,share_pic,share_desc')
                ->where(['module' => $module, 'is_view' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
