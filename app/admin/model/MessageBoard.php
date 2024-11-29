<?php

namespace app\admin\model;

use app\admin\validate\MessageBoard as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class MessageBoard extends Model
{
    //查询所有
    public function all()
    {
        try {
            return $this->field('id,name,field,captcha_id,time,page,date')
                ->where('name', 'LIKE', '%' . Request::get('keyword') . '%')
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2()
    {
        try {
            return $this->field('id,name')->order(['date' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,field,captcha_id,time,page,date')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add()
    {
        $data = [
            'name' => Request::post('name'),
            'field' => implode(',', Request::post('field', [])),
            'captcha_id' => Request::post('captcha_id'),
            'time' => Request::post('time'),
            'page' => Request::post('page'),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if ($data['captcha_id']) {
                $captcha = Config::get('captcha');
                if (!isset($captcha[$data['captcha_id']])) {
                    return '您选择的验证码不存在！';
                }
            }
            return $this->insertGetId($data);
        } else {
            return implode($validate->getError());
        }
    }

    //修改
    public function modify()
    {
        $data = [
            'name' => Request::post('name'),
            'field' => implode(',', Request::post('field', [])),
            'captcha_id' => Request::post('captcha_id'),
            'time' => Request::post('time'),
            'page' => Request::post('page')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if ($data['captcha_id']) {
                $captcha = Config::get('captcha');
                if (!isset($captcha[$data['captcha_id']])) {
                    return '您选择的验证码不存在！';
                }
            }
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return implode($validate->getError());
        }
    }

    //删除
    public function remove()
    {
        try {
            $affectedRows = $this->where('id', 'IN', Request::post('id') ?: Request::post('ids'))->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
