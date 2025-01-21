<?php

namespace app\admin\model;

use app\admin\validate\Manager as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Manager extends Model
{
    //查询总记录
    public function totalCount($type = 0)
    {
        try {
            $map = [];
            if (in_array($type, [1, 2, 3])) {
                $map['level'] = $type;
            } elseif ($type == 4) {
                $map['is_activation'] = 0;
            }
            $total = $this->where($map);
            return $type == 1 ? $total->where('id', '<>', 1)->count() : $total->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            $map['where'] = '`name` LIKE :name';
            $map['value']['name'] = '%' . Request::get('keyword') . '%';
            if (Request::get('level')) {
                $map['where'] .= ' AND `level`=:level';
                $map['value']['level'] = Request::get('level');
            }
            if (Request::get('is_activation', -1) != -1) {
                $map['where'] .= ' AND `is_activation`=:is_activation';
                $map['value']['is_activation'] = Request::get('is_activation');
            }
            if (Request::get('permit_group_id')) {
                $map['where'] .= ' AND `permit_group_id`=:permit_group_id';
                $map['value']['permit_group_id'] = Request::get('permit_group_id');
            }
            if (Request::get('order_permit')) {
                $map['where'] .= ' AND `order_permit`=:order_permit';
                $map['value']['order_permit'] = Request::get('order_permit');
            }
            if (Request::get('bill_permit')) {
                $map['where'] .= ' AND `bill_permit`=:bill_permit';
                $map['value']['bill_permit'] = Request::get('bill_permit');
            }
            if (Request::get('wechat', -1) == 0) {
                $map['where'] .= ' AND `wechat_open_id`=\'\' AND `wechat_union_id`=\'\'';
            } elseif (Request::get('wechat') == 1) {
                $map['where'] .= ' AND (`wechat_open_id`<>\'\' OR `wechat_union_id`<>\'\')';
            }
            if (Request::get('qq', -1) == 0) {
                $map['where'] .= ' AND `qq_open_id`=\'\'';
            } elseif (Request::get('qq') == 1) {
                $map['where'] .= ' AND `qq_open_id`<>\'\'';
            }
            if (Request::get('date1')) {
                $map['where'] .= ' AND `date`>=:date1';
                $map['value']['date1'] = strtotime(Request::get('date1') . ' 00:00:00');
            }
            if (Request::get('date2')) {
                $map['where'] .= ' AND `date`<=:date2';
                $map['value']['date2'] = strtotime(Request::get('date2') . ' 23:59:59');
            }
            return $this->field('id,name,level,is_activation,permit_group_id,order_permit,bill_permit,wechat_open_id,' .
                'wechat_union_id,qq_open_id,date')
                ->where($map['where'], $map['value'])
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2($type = 0)
    {
        try {
            $map = [];
            if ($type == 3) {
                $map['level'] = 3;
            } else {
                $session = Session::get(Config::get('system.session_key_admin') . '.manage_info');
                if ($session['level'] == 2) {
                    if (($type == 1 && $session['order_permit'] != 3) || ($type == 2 && $session['bill_permit'] == 1)) {
                        $map['id'] = $session['id'];
                    }
                }
            }
            return $this->field('id,name')->where($map)->order(['date' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //登录
    public function login()
    {
        try {
            $data = [
                'name' => Request::post('name'),
                'pass' => Request::post('pass')
            ];
            $validate = new validate();
            if ($validate->only(['name', 'pass'])->check($data)) {
                return $this->field('id,name,pass,level,is_activation,permit_group_id,order_permit,bill_permit,' .
                    'wechat_open_id,wechat_union_id,qq_open_id,distributor_code')
                    ->where(['name' => Request::post('name')])
                    ->find();
            } else {
                return $validate->getError();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,pass,email,level,is_activation,permit_group_id,order_permit,bill_permit,' .
                'wechat_open_id,wechat_union_id,qq_open_id,bank,real_name,account,date')
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
            'pass' => Request::post('pass'),
            'repass' => Request::post('repass'),
            'email' => Request::post('email'),
            'level' => Request::post('level'),
            'is_activation' => Request::post('is_activation'),
            'distributor_code' => getKey(10, 1),
            'bank' => Request::post('bank'),
            'real_name' => Request::post('real_name'),
            'account' => Request::post('account'),
            'date' => time()
        ];
        if ($data['level'] == 1) {
            $data['permit_group_id'] = $data['order_permit'] = $data['bill_permit'] = 0;
        } elseif ($data['level'] == 2) {
            if (!Request::post('permit_group_id')) {
                return '请先在权限组模块中添加一个权限组！';
            }
            if (!(new PermitGroup())->one(Request::post('permit_group_id'))) {
                return '您选择的权限组不存在！';
            }
            $data['permit_group_id'] = Request::post('permit_group_id');
            $data['order_permit'] = Request::post('order_permit');
            $data['bill_permit'] = Request::post('bill_permit');
        } else {
            $data['permit_group_id'] = $data['bill_permit'] = 0;
            $data['order_permit'] = 1;
        }
        $validate = new validate();
        $validate = $validate->remove('admin_mail', true);
        if ($data['level'] == 3) {
            $validate = $validate->remove('bill_permit', true);
        }
        if ($validate->check($data)) {
            if ($this->repeat()) {
                return '此账号已存在！';
            }
            $data['pass'] = passEncode(Request::post('pass'));
            unset($data['repass']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //添加创始人
    public function add2($passKey)
    {
        $data = [
            'name' => Request::post('admin_name'),
            'pass' => Request::post('admin_pass'),
            'repass' => Request::post('admin_repass'),
            'admin_mail' => Request::post('admin_mail'),
            'level' => 1,
            'is_activation' => 1,
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->only(['name', 'pass', 'repass', 'admin_mail', 'level', 'is_activation'])->check($data)) {
            $data['pass'] = passEncode(Request::post('admin_pass'), $passKey);
            unset($data['repass'], $data['admin_mail']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //注册
    public function add3()
    {
        if (!Request::post('permit_group_id')) {
            return '请先联系超级管理员在权限组模块中添加一个权限组！';
        }
        $data = [
            'name' => Request::post('name'),
            'pass' => Request::post('pass'),
            'repass' => Request::post('repass'),
            'level' => 2,
            'is_activation' => Config::get('system.register_verify') == 0,
            'permit_group_id' => Request::post('permit_group_id'),
            'order_permit' => 1,
            'bill_permit' => 1,
            'distributor_code' => getKey(10, 1),
            'date' => time()
        ];
        $validate = new validate();
        if (
            $validate->only(['name', 'pass', 'repass', 'level', 'is_activation', 'order_permit', 'bill_permit'])
                ->check($data)
        ) {
            if (!(new PermitGroup())->one($data['permit_group_id'])) {
                return '您选择的权限组不存在！';
            }
            if ($this->repeat()) {
                return '此账号已存在！';
            }
            $data['pass'] = passEncode(Request::post('pass'));
            unset($data['repass']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //修改
    public function modify()
    {
        $scene = ['name', 'email'];
        $data = [
            'name' => Request::post('name'),
            'email' => Request::post('email')
        ];
        if (Request::post('id') != 1) {
            $data['level'] = Request::post('level');
            $data['is_activation'] = Request::post('is_activation');
            $data['bank'] = Request::post('bank');
            $data['real_name'] = Request::post('real_name');
            $data['account'] = Request::post('account');
            $scene[] = 'level';
            $scene[] = 'is_activation';
            $scene[] = 'bank';
            $scene[] = 'real_name';
            $scene[] = 'account';
            if (Request::post('level') == 1) {
                $data['permit_group_id'] = $data['order_permit'] = $data['bill_permit'] = 0;
            } elseif (Request::post('level') == 2) {
                if (!Request::post('permit_group_id')) {
                    return '请先在权限组模块中添加一个权限组！';
                }
                if (!(new PermitGroup())->one(Request::post('permit_group_id'))) {
                    return '您选择的权限组不存在！';
                }
                $data['permit_group_id'] = Request::post('permit_group_id');
                $data['order_permit'] = Request::post('order_permit');
                $data['bill_permit'] = Request::post('bill_permit');
                $scene[] = 'order_permit';
                $scene[] = 'bill_permit';
            } else {
                $data['permit_group_id'] = $data['bill_permit'] = 0;
                $data['order_permit'] = 1;
            }
        }
        if (Request::post('pass')) {
            $data['pass'] = Request::post('pass');
            $data['repass'] = Request::post('repass');
            $scene[] = 'pass';
            $scene[] = 'repass';
        }
        $validate = new validate();
        if ($validate->only($scene)->check($data)) {
            if ($this->repeat(true)) {
                return '此账号已存在！';
            }
            if (Request::post('pass')) {
                $data['pass'] = passEncode(Request::post('pass'));
                unset($data['repass']);
            }
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
    }
    public function modify2()
    {
        $scene[] = 'email';
        $data['email'] = Request::post('email');
        $session = Session::get(Config::get('system.session_key_admin') . '.manage_info');
        $one = $this->one($session['id']);
        if (Request::post('pass')) {
            if (passEncode(Request::post('old_pass')) != $one['pass']) {
                return '请输入正确的旧密码！';
            }
            $data['pass'] = Request::post('pass');
            $data['repass'] = Request::post('repass');
            $scene[] = 'pass';
            $scene[] = 'repass';
        }
        if (Request::post('wechat_open_id')) {
            $data['wechat_open_id'] = $data['wechat_union_id'] = '';
        }
        if (Request::post('qq_open_id')) {
            $data['qq_open_id'] = '';
        }
        $validate = new validate();
        if ($validate->only($scene)->check($data)) {
            if (Request::post('pass')) {
                $data['pass'] = passEncode(Request::post('pass'));
                unset($data['repass']);
            }
            return $this->where(['id' => $session['id']])->update($data);
        } else {
            return $validate->getError();
        }
    }
    public function modify3()
    {
        if (!Request::post('reset_pass_key')) {
            return '重置密钥不得为空！';
        }
        if (Request::post('reset_pass_key') != Config::get('system.reset_pass_key')) {
            return '重置密钥不正确！';
        }
        $data = [
            'pass' => Request::post('pass'),
            'repass' => Request::post('repass')
        ];
        $validate = new validate();
        if ($validate->only(['pass', 'repass'])->check($data)) {
            $data['pass'] = passEncode(Request::post('pass'));
            unset($data['repass']);
            return $this->where(['id' => 1])->update($data);
        } else {
            return $validate->getError();
        }
    }

    //激活和取消激活
    public function isActivation($isActivation)
    {
        return $this->where(['id' => Request::post('id')])->update(['is_activation' => $isActivation]);
    }

    //绑定和解绑微信
    public function wechatOpenId($wechatOpenId = '', $wechatUnionId = '', $id = 0)
    {
        return $this->where(['id' => $id ?: Request::post('id')])
            ->update(['wechat_open_id' => $wechatOpenId, 'wechat_union_id' => $wechatUnionId]);
    }

    //绑定和解绑QQ
    public function qqOpenId($qqOpenId = '', $id = 0)
    {
        return $this->where(['id' => $id ?: Request::post('id')])->update(['qq_open_id' => $qqOpenId]);
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
