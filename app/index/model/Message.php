<?php

namespace app\index\model;

use app\index\validate\Message as validate;
use Exception;
use think\captcha\facade\Captcha;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class Message extends Model
{
    //查询总记录
    public function totalCount($id = 0)
    {
        try {
            return $this->where(['message_board_id' => $id ?: Request::get('message_board_id'), 'is_view' => 1])
                ->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all($pageSize)
    {
        try {
            return $this->field('id,name,text_id_content,text_id_reply,date')
                ->where(['message_board_id' => Request::get('message_board_id'), 'is_view' => 1])
                ->order(['date' => 'DESC'])
                ->paginate($pageSize ?: 10);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add()
    {
        $messageBoardOne = (new MessageBoard())->one(Request::post('message_board_id'));
        if (!$messageBoardOne) {
            return '不存在此留言板！';
        }
        $scene = [];
        $data = [
            'ip' => getUserIp(),
            'message_board_id' => Request::post('message_board_id'),
            'date' => time()
        ];
        $fieldArr = explode(',', $messageBoardOne['field']);
        if (in_array(0, $fieldArr)) {
            $data['name'] = Request::post('name');
            $scene[] = 'name';
        }
        if (in_array(1, $fieldArr)) {
            $data['tel'] = Request::post('tel');
            $scene[] = 'tel';
        }
        if (in_array(2, $fieldArr)) {
            $data['email'] = Request::post('email');
            $scene[] = 'email';
        }
        if (in_array(3, $fieldArr)) {
            $data['content'] = Request::post('content');
            $scene[] = 'content';
        }
        if ($messageBoardOne['captcha_id']) {
            $data['captcha'] = Request::post('captcha');
            $scene[] = 'captcha';
        }
        $validate = new validate();
        if ($validate->only($scene)->check($data)) {
            if ($messageBoardOne['captcha_id']) {
                $captcha = Config::get('captcha');
                if (
                    isset($captcha[$messageBoardOne['captcha_id']]) &&
                    !Captcha::check(Request::post('captcha'), $messageBoardOne['captcha_id'])
                ) {
                    return '验证码有误！';
                }
            }
            if ($this->repeat($messageBoardOne['time'])) {
                return '请勿频繁提交留言！';
            }
            $data['text_id_content'] = (new Text())->add($data['content']);
            unset($data['content'], $data['captcha']);
            return $this->insertGetId($data);
        } else {
            return implode($validate->getError());
        }
    }

    //验证重复
    private function repeat($time)
    {
        try {
            return $this->field('id')
                ->where(['ip' => getUserIp(), 'message_board_id' => Request::post('message_board_id')])
                ->where('date', '>=', time() - $time)
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
