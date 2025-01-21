<?php

namespace app\admin\model;

use app\admin\validate\Message as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Message extends Model
{
    //按不同状态查询记录数
    public function totalCount($isView = -1)
    {
        try {
            $map = [];
            if ($isView != -1) {
                $map['is_view'] = $isView;
            }
            return $this->where($map)->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            $tablePrefix = Config::get('database.connections.mysql.prefix');
            $map = [
                'where' => '(`name` LIKE :name OR `tel` LIKE :tel OR `email` LIKE :email OR `text_id_content` IN ' .
                    '(SELECT `id` FROM `' . $tablePrefix . 'text` WHERE `content` LIKE :content) OR `text_id_reply` ' .
                    'IN (SELECT `id` FROM `' . $tablePrefix . 'text` WHERE `content` LIKE :reply) OR `ip` LIKE :ip)',
                'value' => [
                    'name' => '%' . Request::get('keyword') . '%',
                    'tel' => '%' . Request::get('keyword') . '%',
                    'email' => '%' . Request::get('keyword') . '%',
                    'content' => '%' . Request::get('keyword') . '%',
                    'reply' => '%' . Request::get('keyword') . '%',
                    'ip' => '%' . Request::get('keyword') . '%'
                ]
            ];
            if (Request::get('message_board_id')) {
                $map['where'] .= ' AND `message_board_id`=:message_board_id';
                $map['value']['message_board_id'] = Request::get('message_board_id');
            }
            if (Request::get('is_view', -1) != -1) {
                $map['where'] .= ' AND `is_view`=:is_view';
                $map['value']['is_view'] = Request::get('is_view');
            }
            return $this->field('id,name,tel,email,text_id_content,ip,message_board_id,text_id_reply,is_view,date')
                ->where($map['where'], $map['value'])
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
            return $this->field('id,name,tel,email,text_id_content,ip,message_board_id,text_id_reply,is_view,date')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //修改
    public function modify($textIdContent = 0, $textIdReply = 0)
    {
        $data = [
            'name' => Request::post('name'),
            'tel' => Request::post('tel'),
            'email' => Request::post('email'),
            'content' => Request::post('content'),
            'reply' => Request::post('reply')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            $Text = new Text();
            $data['text_id_content'] = $Text->amr($data['content'], $textIdContent);
            $data['text_id_reply'] = $Text->amr($data['reply'], $textIdReply);
            unset($data['content'], $data['reply']);
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
    }

    //确认和取消显示
    public function isView($isView, $id = 0)
    {
        return $this->where(['id' => $id ?: Request::post('id')])->update(['is_view' => $isView]);
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
