<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class Template extends Model
{
    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,type,name,template,template_style_id,product_type,product_sort_ids,product_ids,' .
                'product_default,product_view_type,field_ids,payment_ids,payment_default,is_show_search,is_show_send,' .
                'captcha_id,is_sms_verify,is_sms_notify,success,success2,often,is_default,date')
                ->where(['id' => $id ?: Request::get('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
