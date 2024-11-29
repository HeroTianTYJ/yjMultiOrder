<?php

namespace app\admin\model;

use app\admin\validate\Product as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Product extends Model
{
    //按分类查询记录数
    public function totalCount($productSortId = 0)
    {
        try {
            return $this->where(['product_sort_id' => $productSortId])->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //按是否运作查询记录数
    public function totalCount2($isView = 0)
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
            $map = [
                'where' => '`name` LIKE :name',
                'value' => [
                    'name' => '%' . Request::get('keyword') . '%'
                ]
            ];
            if (Request::get('product_sort_id')) {
                $map['where'] .= ' AND `product_sort_id`=:product_sort_id';
                $map['value']['product_sort_id'] = Request::get('product_sort_id');
            }
            return $this->field('id,name,product_sort_id,price,price2,commission,color,low_price,high_price,sort,' .
                'is_view,is_default,date')
                ->where($map['where'], $map['value'])
                ->order(['sort' => 'ASC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询分类下的商品
    public function all2($productSortId = 0)
    {
        try {
            return $this->field('id,name,price,commission,color,text_id_attr,low_price,high_price,text_id_own_price,' .
                'is_default')
                ->where(['product_sort_id' => $productSortId])
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
            return $this->field('id,name,product_sort_id,price,price2,commission,color,email,text_id_attr,low_price,' .
                'high_price,text_id_own_price,sort,is_view,is_default,date')
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
        $attr = '';
        if (Request::post('attr_v')) {
            $attrV = Request::post('attr_v');
            foreach (Request::post('attr_k') as $key => $value) {
                if ($value) {
                    $attr .= $value . ':' . implode(',', array_filter($attrV[$key])) . '|';
                }
            }
        }
        $data = [
            'name' => Request::post('name'),
            'product_sort_id' => Request::post('product_sort_id'),
            'price' => Request::post('price'),
            'price2' => Request::post('price2'),
            'commission' => Request::post('commission'),
            'color' => Request::post('color'),
            'email' => Request::post('email'),
            'attr' => substr($attr, 0, -1),
            'sort' => $this->nextId(),
            'is_view' => 1,
            'date' => time()
        ];
        if (Request::post('own_price')) {
            $data['low_price'] = Request::post('low_price');
            $data['high_price'] = Request::post('high_price');
            $data['own_price'] = json_encode(Request::post('own_price'));
        }
        $validate = new validate();
        if ($validate->check($data)) {
            if (!(new ProductSort())->one($data['product_sort_id'])) {
                return '您选择的商品分类不存在！';
            }
            if ($this->repeat(true)) {
                return '此商品已存在！';
            }
            $Text = new Text();
            $data['text_id_attr'] = $Text->amr($data['attr']);
            unset($data['attr']);
            if (Request::post('own_price')) {
                $data['text_id_own_price'] = $Text->amr($data['own_price']);
                unset($data['own_price']);
            }
            return $this->insertGetId($data);
        } else {
            return implode($validate->getError());
        }
    }

    //修改
    public function modify($textIdAttr = 0, $textIdOwnPrice = 0)
    {
        $attr = '';
        if (Request::post('attr_v')) {
            $attrV = Request::post('attr_v');
            foreach (Request::post('attr_k') as $key => $value) {
                if ($value) {
                    $attr .= $value . ':' . implode(',', array_filter($attrV[$key])) . '|';
                }
            }
        }
        $data = [
            'name' => Request::post('name'),
            'product_sort_id' => Request::post('product_sort_id'),
            'price' => Request::post('price'),
            'price2' => Request::post('price2'),
            'commission' => Request::post('commission'),
            'color' => Request::post('color'),
            'email' => Request::post('email'),
            'attr' => substr($attr, 0, -1)
        ];
        if (Request::post('own_price')) {
            $data['low_price'] = Request::post('low_price');
            $data['high_price'] = Request::post('high_price');
            $data['own_price'] = json_encode(Request::post('own_price'));
        } else {
            $data['low_price'] = $data['high_price'] = 0;
            $data['own_price'] = '';
        }
        $validate = new validate();
        if ($validate->check($data)) {
            if (!(new ProductSort())->one($data['product_sort_id'])) {
                return '您选择的商品分类不存在！';
            }
            if ($this->repeat(true)) {
                return '此商品已存在！';
            }
            $Text = new Text();
            $data['text_id_attr'] = $Text->amr($data['attr'], $textIdAttr);
            unset($data['attr']);
            if (Request::post('own_price')) {
                $data['text_id_own_price'] = $Text->amr($data['own_price'], $textIdOwnPrice);
            }
            unset($data['own_price']);
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return implode($validate->getError());
        }
    }

    //设置默认
    public function isDefault()
    {
        $this->where(['is_default' => 1])->update(['is_default' => 0]);
        return $this->where(['id' => Request::post('id')])->update(['is_default' => 1]);
    }

    //确认和取消显示
    public function isView($isView)
    {
        return $this->where(['id' => Request::post('id')])->update(['is_view' => $isView]);
    }

    //排序
    public function sort($id, $sort)
    {
        return $this->where(['id' => $id])->update(['sort' => $sort]);
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

    //验证重复
    private function repeat($update = false)
    {
        try {
            $one = $this->field('id')->where([
                'name' => Request::post('name'),
                'product_sort_id' => Request::post('product_sort_id')
            ]);
            return $update ? $one->where('id', '<>', Request::post('id'))->find() : $one->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
