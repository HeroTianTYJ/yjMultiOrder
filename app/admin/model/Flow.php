<?php

namespace app\admin\model;

use app\admin\validate\Flow as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Flow extends Model
{
    //查询所有
    public function all()
    {
        try {
            return $this->field('id,name,price,date')
                ->where('name|price', 'LIKE', '%' . Request::get('keyword') . '%')
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,price,text_id_note,date')->where(['id' => $id ?: Request::post('id')])->find();
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
            'price' => Request::post('price'),
            'note' => Request::post('note'),
            'date' => checkTime(Request::post('date'))
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            $data['text_id_note'] = (new Text())->amr($data['note']);
            unset($data['note']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //修改
    public function modify($textIdNote = 0)
    {
        $data = [
            'name' => Request::post('name'),
            'price' => Request::post('price'),
            'note' => Request::post('note'),
            'date' => checkTime(Request::post('date'))
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            $data['text_id_note'] = (new Text())->amr($data['note'], $textIdNote);
            unset($data['note']);
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
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
