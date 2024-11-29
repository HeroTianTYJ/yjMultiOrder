<?php

namespace app\distributor\model;

use app\distributor\validate\Manager as validate;
use Exception;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Manager extends Model
{
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
                return $this->field('id,name,pass,level,is_activation,qq_open_id,wechat_open_id,wechat_union_id,' .
                    'distributor_code')
                    ->where(['name' => Request::post('name')])
                    ->find();
            } else {
                return implode($validate->getError());
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one()
    {
        try {
            return $this->field('id,name,pass,email,qq_open_id,wechat_open_id,wechat_union_id,bank,real_name,account,' .
                'date')
                ->where(['id' => Session::get(Config::get('system.session_key_distributor') . '.manage_info.id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //注册
    public function add()
    {
        $data = [
            'name' => Request::post('name'),
            'pass' => Request::post('pass'),
            'repass' => Request::post('repass'),
            'email' => Request::post('email'),
            'is_activation' => Config::get('system.register_verify') == 0,
            'level' => 3,
            'distributor_code' => getKey(10, 1),
            'bank' => Request::post('bank'),
            'real_name' => Request::post('real_name'),
            'account' => Request::post('account'),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if ($this->repeat()) {
                return '此账号已存在！';
            }
            $data['pass'] = passEncode(Request::post('pass'));
            unset($data['repass']);
            return $this->insertGetId($data);
        } else {
            return implode($validate->getError());
        }
    }

    //修改
    public function modify()
    {
        $scene[] = 'email';
        $data['email'] = Request::post('email');
        $one = $this->one();
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
        if (!$one['bank'] || !$one['real_name'] || !$one['account']) {
            $data['bank'] = Request::post('bank');
            $data['real_name'] = Request::post('real_name');
            $data['account'] = Request::post('account');
            $scene[] = 'bank';
            $scene[] = 'real_name';
            $scene[] = 'account';
        }
        $validate = new validate();
        if ($validate->only($scene)->check($data)) {
            if (Request::post('pass')) {
                $data['pass'] = passEncode(Request::post('pass'));
                unset($data['repass']);
            }
            return $this->where(['id' => $one['id']])->update($data);
        } else {
            return implode($validate->getError());
        }
    }

    //绑定和解绑微信
    public function wechatOpenId($wechatOpenId = '', $wechatUnionId = '', $id = 0)
    {
        return $this->where(['id' => $id])
            ->update(['wechat_open_id' => $wechatOpenId, 'wechat_union_id' => $wechatUnionId]);
    }

    //绑定和解绑QQ
    public function qqOpenId($qqOpenId = '', $id = 0)
    {
        return $this->where(['id' => $id])->update(['qq_open_id' => $qqOpenId]);
    }

    //验证重复
    private function repeat()
    {
        try {
            return $this->field('id')->where(['name' => Request::post('name')])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
