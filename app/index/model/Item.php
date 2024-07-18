<?php

namespace app\index\model;

use Exception;
use think\facade\Request;
use think\Model;

class Item extends Model
{
    //查询总记录
    public function totalCount($ids = '')
    {
        try {
            $total = $this->where('is_view', 1);
            return $ids ? $total->where('id', 'IN', $ids)->count() : $total->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function totalCount2($brandId = 0)
    {
        try {
            return $this->where(['brand_id' => $brandId])->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all($ids = '', $firstRow = 0, $pageSize = 0)
    {
        try {
            $all = $this->field('id,name,preview,tag,tag_bg,price1,price2')
                ->where('is_view', 1)
                ->order(['date' => 'DESC']);
            if ($ids) {
                $all = $all->where('id', 'IN', $ids);
            }
            return $pageSize == 0 ?
                $all->select()->toArray() : $all->limit($firstRow, $pageSize)->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function all2($brandId = 0, $firstRow = 0, $pageSize = 0)
    {
        try {
            $all = $this->field('id,name,preview,tag,tag_bg,price1,price2')
                ->where(['brand_id' => $brandId, 'is_view' => 1])
                ->order(['date' => 'DESC']);
            return $pageSize == 0 ?
                $all->select()->toArray() : $all->limit($firstRow, $pageSize)->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,brand_id,preview,tag,tag_bg,is_show_price,price1,price2,sale,sale_minute,' .
                'sale_count,countdown1,countdown2,is_show_send,title,keyword,description,width,bg_color,copyright,' .
                'code_type,text_id_code,tel,sms,qq,picture,text_id_buy,text_id_procedure,text_id_introduce,' .
                'text_id_service,message_board_id,comment_type,text_id_comment,column_name1,text_id_column_content1,' .
                'column_name2,text_id_column_content2,column_name3,text_id_column_content3,column_name4,' .
                'text_id_column_content4,column_name5,text_id_column_content5,template_id,product_type,' .
                'product_sort_ids,product_ids,product_default,product_view_type,sort,text_id_nav,icon,share_title,' .
                'share_pic,share_desc,date')
                ->where(['id' => $id ?: Request::param('id'), 'is_view' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function one2($id = 0)
    {
        try {
            return $this->field('is_distribution')
                ->where(['id' => $id ?: Request::param('id'), 'is_view' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
