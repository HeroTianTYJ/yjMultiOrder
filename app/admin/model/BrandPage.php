<?php

namespace app\admin\model;

use app\admin\validate\BrandPage as validate;
use Exception;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class BrandPage extends Model
{
    //查询所有
    public function all()
    {
        try {
            return $this->field('id,name,width,left_width,bg_color,page')
                ->where(['id' => 1])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询图片
    public function picture()
    {
        try {
            return $this->field('`copyright`,`icon`,CONCAT(\'[img=\',`share_pic`,\']\')')
                ->where(['id' => 1])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one()
    {
        try {
            return $this->field('id,name,title,keyword,description,width,left_width,bg_color,page,copyright,' .
                'code_type,text_id_code,text_id_nav,icon,share_title,share_pic,share_desc')
                ->where(['id' => 1])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //修改
    public function modify($textIdCode = 0, $textIdNav = 0)
    {
        $data = [
            'name' => Request::post('name'),
            'title' => Request::post('title'),
            'keyword' => Request::post('keyword'),
            'description' => Request::post('description'),
            'width' => Request::post('width'),
            'left_width' => Request::post('left_width'),
            'bg_color' => Request::post('bg_color'),
            'page' => Request::post('page'),
            'copyright' => Request::post('copyright', '', 'stripslashes'),
            'code_type' => Request::post('code_type'),
            'code' => Request::post('code', '', 'stripslashes'),
            'nav' => trim(Request::post('nav', '', 'stripslashes')),
            'icon' => trim(Request::post('icon')),
            'share_title' => Request::post('share_title'),
            'share_pic' => Request::post('share_pic'),
            'share_desc' => Request::post('share_desc')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (count(explode("\r\n", $data['nav'])) > 3) {
                return '最多只能设置3个底部导航链接！';
            }
            if (count(explode("\r\n", $data['icon'])) > 3) {
                return '最多只能设置3个底部导航图标！';
            }
            $Text = new Text();
            $data['text_id_code'] = $Text->amr($data['code'], $textIdCode);
            $data['text_id_nav'] = $Text->amr($data['nav'], $textIdNav);
            unset($data['code'], $data['nav']);
            return $this->where(['id' => 1])->update($data);
        } else {
            return implode($validate->getError());
        }
    }
}
