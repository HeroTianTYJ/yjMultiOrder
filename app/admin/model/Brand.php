<?php

namespace app\admin\model;

use app\admin\validate\Brand as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Brand extends Model
{
    //按是否运作查询记录数
    public function totalCount($isView = 0)
    {
        try {
            return $isView ? $this->where(['is_view' => 1])->count() : $this->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            $map['where'] = '`name` LIKE :keyword';
            $map['value']['keyword'] = '%' . Request::get('keyword') . '%';
            if (Request::get('category_id')) {
                $map['where'] .= ' AND `category_id`=:category_id';
                $map['value']['category_id'] = Request::get('category_id');
            }
            return $this->field('id,name,color,logo,category_id,sort,is_view,date')
                ->where($map['where'], $map['value'])
                ->order(['sort' => 'ASC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2($categoryId = 0)
    {
        try {
            return $this->field('id,name,color')
                ->where(['category_id' => $categoryId])
                ->order(['sort' => 'ASC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询图片
    public function picture()
    {
        try {
            return $this->field('CONCAT(\'[img=\',`logo`,\']\')')->where('logo', '<>', '')->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,color,logo,category_id,sort,is_view,date')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function one2($id = 0)
    {
        try {
            return $this->field('name,color,category_id')->where(['id' => $id])->find();
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
            'color' => Request::post('color'),
            'logo' => Request::post('logo'),
            'category_id' => Request::post('category_id'),
            'sort' => $this->nextId(),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            $categoryOne = (new Category())->one($data['category_id']);
            if (!$categoryOne || $categoryOne['parent_id'] == 0) {
                return '您选择的二级品牌分类不存在！';
            }
            if ($this->repeat($data['name'])) {
                return '此品牌已存在！';
            }
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //批量添加
    public function multi()
    {
        $name = explode("\r\n", Request::post('name'));
        $logo = explode(',', Request::post('logo'));
        if (count($name) != count($logo)) {
            return '您输入的品牌名称个数和插入的品牌logo个数不一致！';
        }
        $validate = new validate();
        foreach ($name as $key => $value) {
            $data = [
                'name' => $value,
                'color' => Request::post('color'),
                'logo' => $logo[$key],
                'category_id' => Request::post('category_id'),
                'sort' => $this->nextId(),
                'date' => time()
            ];
            if ($validate->check($data)) {
                $categoryOne = (new Category())->one($data['category_id']);
                if (!$categoryOne || $categoryOne['parent_id'] == 0) {
                    return '您选择的二级品牌分类不存在！';
                }
                if ($this->repeat($value)) {
                    return '“' . $value . '”品牌已存在！';
                }
                $this->insertGetId($data);
            } else {
                return $validate->getError();
            }
        }
        return 1;
    }

    //修改
    public function modify()
    {
        $data = [
            'name' => Request::post('name'),
            'color' => Request::post('color'),
            'logo' => Request::post('logo'),
            'category_id' => Request::post('category_id')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            $categoryOne = (new Category())->one($data['category_id']);
            if (!$categoryOne || $categoryOne['parent_id'] == 0) {
                return '您选择的二级品牌分类不存在！';
            }
            if ($this->repeat($data['name'], true)) {
                return '此品牌已存在！';
            }
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
    }

    //排序
    public function sort($id, $sort)
    {
        return $this->where(['id' => $id])->update(['sort' => $sort]);
    }

    //确认和取消显示
    public function isView($isView)
    {
        return $this->where(['id' => Request::post('id')])->update(['is_view' => $isView]);
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

    //验证重复
    private function repeat($name, $update = false)
    {
        try {
            $one = $this->field('id')->where(['name' => $name, 'category_id' => Request::post('category_id')]);
            return $update ? $one->where('id', '<>', Request::post('id'))->find() : $one->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //自增ID
    private function nextId()
    {
        try {
            $query = Db::query('SHOW TABLE STATUS FROM `' . Config::get('database.connections.mysql.database') .
                '` LIKE \'' . $this->getTable() . '\'');
            return $query[0]['Auto_increment'];
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
