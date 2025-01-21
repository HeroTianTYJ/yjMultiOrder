<?php

namespace app\admin\model;

use app\admin\validate\Category as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Category extends Model
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

    //查询所有（主分类）
    public function all()
    {
        try {
            return $this->field('id,name,color,sort,is_view,is_default,date')
                ->where(
                    '`name` LIKE :keyword AND `parent_id`=:parent_id',
                    ['keyword' => '%' . Request::get('keyword') . '%', 'parent_id' => Request::get('parent_id', 0)]
                )
                ->order(['sort' => 'ASC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2($parentId = 0)
    {
        try {
            return $this->field('id,name,color')
                ->where(['parent_id' => $parentId])
                ->order(['sort' => 'ASC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,color,parent_id,sort,is_view,is_default,date')
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
            return $this->field('name,color,parent_id')->where(['id' => $id])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条子分类
    public function one3($parentId)
    {
        try {
            return $this->field('id')->where(['parent_id' => $parentId])->find();
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
            'parent_id' => Request::post('parent_id'),
            'sort' => $this->nextId(),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if ($this->repeat(Request::post('name'))) {
                return '此品牌分类已存在！';
            }
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //批量添加
    public function multi()
    {
        $validate = new validate();
        foreach (explode("\r\n", Request::post('name')) as $value) {
            $data = [
                'name' => $value,
                'color' => Request::post('color'),
                'parent_id' => Request::post('parent_id'),
                'sort' => $this->nextId(),
                'date' => time()
            ];
            if ($validate->check($data)) {
                if ($this->repeat($value)) {
                    return '“' . $value . '”品牌分类已存在！';
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
            'parent_id' => Request::post('parent_id')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (Request::post('id') == $data['parent_id']) {
                return '无法移动到自身品牌分类！';
            }
            if ($data['parent_id'] && $this->one3(Request::post('id'))) {
                return '此品牌分类下有子品牌分类，无法移动！';
            }
            if ($this->repeat(Request::post('name'), true)) {
                return '此品牌分类已存在！';
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

    //设置默认
    public function isDefault()
    {
        $this->where(['is_default' => 1])->update(['is_default' => 0]);
        return $this->where(['id' => Request::post('id')])->update(['is_default' => 1]);
    }

    //删除
    public function remove()
    {
        try {
            $affectedRows = $this->where('id', 'IN', Request::post('id') ?: Request::post('ids'))->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
        return $affectedRows;
    }

    //验证重复
    private function repeat($name, $update = false)
    {
        try {
            $one = $this->field('id')->where(['name' => $name, 'parent_id' => Request::post('parent_id')]);
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
