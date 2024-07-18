<?php

namespace app\admin\model;

use app\admin\validate\Wxxcx as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Wxxcx extends Model
{
    //查询总记录
    public function totalCount()
    {
        try {
            return $this->count();
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
            if (Request::get('type', -1) == 0) {
                $map['where'] .= ' AND `type`=0';
                if (Request::get('lists_id')) {
                    $map['where'] .= ' AND `page_id`=:page_id';
                    $map['value']['page_id'] = Request::get('lists_id');
                }
            } elseif (Request::get('type') == 1) {
                $map['where'] .= ' AND `type`=1';
                if (Request::get('item_id')) {
                    $map['where'] .= ' AND `page_id`=:page_id';
                    $map['value']['page_id'] = Request::get('item_id');
                }
            } elseif (Request::get('type') == 2) {
                $map['where'] .= ' AND `type`=2';
            }
            return $this->field('id,name,type,page_id,zip,date')
                ->where($map['where'], $map['value'])
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
            return $this->field('id,name')->order(['id' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,type,page_id,submit_key,app_id,app_secret,pay_mch_id,pay_key,' .
                'pay_cert_serial_number,text_id_pay_cert_private_key,zip,date')
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
            'type' => Request::post('type'),
            'submit_key' => Request::post('submit_key'),
            'app_id' => Request::post('app_id'),
            'app_secret' => Request::post('app_secret'),
            'pay_mch_id' => Request::post('pay_mch_id'),
            'pay_key' => Request::post('pay_key'),
            'pay_cert_serial_number' => Request::post('pay_cert_serial_number'),
            'pay_cert_private_key' => Request::post('pay_cert_private_key'),
            'date' => time()
        ];
        if ($data['type'] == 0) {
            if (!Request::post('lists_id')) {
                return '请先在列表页模块中至少添加一个列表页！';
            }
            $data['page_id'] = Request::post('lists_id');
        } elseif ($data['type'] == 1) {
            if (!Request::post('item_id')) {
                return '请先在商品页模块中至少添加一个商品页！';
            }
            $data['page_id'] = Request::post('item_id');
        } elseif ($data['type'] == 2) {
            $data['page_id'] = 0;
        }
        $validate = new validate();
        if ($validate->check($data)) {
            if ($this->repeat()) {
                return '此微信小程序已存在！';
            }
            $data['text_id_pay_cert_private_key'] = (new Text())->amr($data['pay_cert_private_key']);
            unset($data['pay_cert_private_key']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //修改
    public function modify($textIdPayCertPrivateKey = 0)
    {
        $data = [
            'name' => Request::post('name'),
            'type' => Request::post('type'),
            'submit_key' => Request::post('submit_key'),
            'app_id' => Request::post('app_id'),
            'app_secret' => Request::post('app_secret'),
            'pay_mch_id' => Request::post('pay_mch_id'),
            'pay_key' => Request::post('pay_key'),
            'pay_cert_serial_number' => Request::post('pay_cert_serial_number'),
            'pay_cert_private_key' => Request::post('pay_cert_private_key')
        ];
        if ($data['type'] == 0) {
            if (!Request::post('lists_id')) {
                return '请先在列表页模块中至少添加一个列表页！';
            }
            $data['page_id'] = Request::post('lists_id');
        } elseif ($data['type'] == 1) {
            if (!Request::post('item_id')) {
                return '请先在商品页模块中至少添加一个商品页！';
            }
            $data['page_id'] = Request::post('item_id');
        } elseif ($data['type'] == 2) {
            $data['page_id'] = 0;
        }
        $validate = new validate();
        if ($validate->check($data)) {
            if ($this->repeat(true)) {
                return '此微信小程序已存在！';
            }
            $data['text_id_pay_cert_private_key'] =
                (new Text())->amr($data['pay_cert_private_key'], $textIdPayCertPrivateKey);
            unset($data['pay_cert_private_key']);
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
    }
    public function modify2($zip = '')
    {
        return $this->where(['id' => Request::post('id')])->update(['zip' => $zip]);
    }
    public function modify3($token = '')
    {
        return $this->where(['id' => Request::post('id')])->update(['token' => $token, 'token_time' => time()]);
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
