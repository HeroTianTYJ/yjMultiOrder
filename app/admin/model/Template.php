<?php

namespace app\admin\model;

use app\admin\validate\Template as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Template extends Model
{
    //查询所有
    public function all()
    {
        try {
            return $this->field('id,type,name,template,template_style_id,is_show_search,is_show_send,captcha_id,' .
                'is_sms_verify,is_sms_notify,is_default,date')
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
            return $this->field('id,name,is_default')->order(['date' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,type,name,template,template_style_id,product_type,product_sort_ids,product_ids,' .
                'product_default,product_view_type,field_ids,payment_ids,payment_default,is_show_search,is_show_send,' .
                'captcha_id,is_sms_verify,is_sms_notify,success,success2,often,is_default,date')
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
            'type' => Request::post('type'),
            'name' => Request::post('name'),
            'template' => Request::post('template'),
            'template_style_id' => Request::post('template_style_id'),
            'product_type' => Request::post('product_type'),
            'product_view_type' => Request::post('product_view_type'),
            'field_ids' => implode(',', Request::post('field_ids', [])),
            'payment_ids' => implode(',', Request::post('payment_ids', [])),
            'payment_default' => Request::post('payment_default'),
            'is_show_search' => Request::post('is_show_search'),
            'is_show_send' => Request::post('is_show_send'),
            'captcha_id' => Request::post('captcha_id'),
            'is_sms_verify' => Request::post('is_sms_verify'),
            'is_sms_notify' => Request::post('is_sms_notify'),
            'success' => Request::post('success', '', null),
            'success2' => Request::post('success2', '', null),
            'often' => Request::post('often', '', null),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (!(new TemplateStyle())->one($data['template_style_id'])) {
                return '您选择的模板样式不存在！';
            }
            if ($data['type'] == 1) {
                if ($data['product_type'] == 0) {
                    if (!Request::post('product_ids1')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_id');
                    $data['product_ids'] = Request::post('product_ids1');
                    $data['product_default'] = Request::post('product_default1');
                } elseif ($data['product_type'] == 1) {
                    if (!Request::post('product_ids2')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_ids');
                    $data['product_ids'] = Request::post('product_ids2');
                    $data['product_default'] = Request::post('product_default2');
                }
                foreach (explode(',', $data['product_sort_ids']) as $value) {
                    if (!(new ProductSort())->one($value)) {
                        return '您勾选的商品分类不存在！';
                    }
                }
                foreach (explode(',', $data['product_ids']) as $value) {
                    if (!(new Product())->one($value)) {
                        return '您勾选的商品不存在！';
                    }
                }
                if (!in_array($data['product_default'], explode(',', $data['product_ids']))) {
                    return '您选择的默认商品不存在！';
                }
            }
            if ($data['captcha_id']) {
                $captcha = Config::get('captcha');
                if (!isset($captcha[$data['captcha_id']])) {
                    return '您选择的验证码不存在！';
                }
            }
            if ($this->repeat()) {
                return '此模板名称已存在！';
            }
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //修改
    public function modify()
    {
        $data = [
            'type' => Request::post('type'),
            'name' => Request::post('name'),
            'template' => Request::post('template'),
            'template_style_id' => Request::post('template_style_id'),
            'product_type' => Request::post('product_type'),
            'product_view_type' => Request::post('product_view_type'),
            'field_ids' => implode(',', Request::post('field_ids', [])),
            'payment_ids' => implode(',', Request::post('payment_ids', [])),
            'payment_default' => Request::post('payment_default'),
            'is_show_search' => Request::post('is_show_search'),
            'is_show_send' => Request::post('is_show_send'),
            'captcha_id' => Request::post('captcha_id'),
            'is_sms_verify' => Request::post('is_sms_verify'),
            'is_sms_notify' => Request::post('is_sms_notify'),
            'success' => Request::post('success', '', null),
            'success2' => Request::post('success2', '', null),
            'often' => Request::post('often', '', null)
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (!(new TemplateStyle())->one($data['template_style_id'])) {
                return '您选择的模板样式不存在！';
            }
            if ($data['type'] == 1) {
                if ($data['product_type'] == 0) {
                    if (!Request::post('product_ids1')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_id');
                    $data['product_ids'] = Request::post('product_ids1');
                    $data['product_default'] = Request::post('product_default1');
                } elseif ($data['product_type'] == 1) {
                    if (!Request::post('product_ids2')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_ids');
                    $data['product_ids'] = Request::post('product_ids2');
                    $data['product_default'] = Request::post('product_default2');
                }
                foreach (explode(',', $data['product_sort_ids']) as $value) {
                    if (!(new ProductSort())->one($value)) {
                        return '您勾选的商品分类不存在！';
                    }
                }
                foreach (explode(',', $data['product_ids']) as $value) {
                    if (!(new Product())->one($value)) {
                        return '您勾选的商品不存在！';
                    }
                }
                if (!in_array($data['product_default'], explode(',', $data['product_ids']))) {
                    return '您选择的默认商品不存在！';
                }
            } else {
                $data['product_sort_ids'] = $data['product_ids'] = '';
                $data['product_default'] = 0;
            }
            if ($data['captcha_id']) {
                $captcha = Config::get('captcha');
                if (!isset($captcha[$data['captcha_id']])) {
                    return '您选择的验证码不存在！';
                }
            }
            if ($this->repeat(true)) {
                return '此模板名称已存在！';
            }
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
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
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //验证重复
    private function repeat($update = false)
    {
        try {
            $one = $this->field('id')->where(['name' => Request::post('name')]);
            return $update ? $one->where('id', '<>', Request::post('id'))->find() : $one->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
