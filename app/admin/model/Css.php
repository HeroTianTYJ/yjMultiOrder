<?php

namespace app\admin\model;

use Exception;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class Css extends Model
{
    //查询所有
    public function all()
    {
        try {
            $map = [
                'where' => '(CONCAT(`filename`,\'.css\') LIKE :css OR `description` LIKE :description)',
                'value' => [
                    'css' => '%' . Request::get('keyword') . '%',
                    'description' => '%' . Request::get('keyword') . '%'
                ]
            ];
            if (Request::get('type', -1) != -1) {
                $map['where'] .= ' AND `type`=:type';
                $map['value']['type'] = Request::get('type');
            }
            return $this->field('`id`,`type`,CONCAT(`filename`,\'.css\') `filename`,`description`')
                ->where($map['where'], $map['value'])
                ->order(['id' => 'ASC'])
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
            return $this->field('`id`,`type`,CONCAT(`filename`,\'.css\') `filename`,`description`')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add($type, $filename, $description)
    {
        if ($this->repeat($type, $filename)) {
            return 0;
        }
        return $this->insertGetId([
            'type' => $type,
            'filename' => $filename,
            'description' => $description
        ]);
    }

    //验证重复
    private function repeat($type, $filename)
    {
        try {
            return $this->field('id')->where(['type' => $type, 'filename' => $filename])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
