<?php

namespace app\index\controller;

use think\facade\Request;
use app\index\model;

class Message extends Base
{
    public function index()
    {
        if (Request::isAjax()) {
            $messageBoardOne = (new model\MessageBoard())->one(Request::get('message_board_id'));
            if (!$messageBoardOne) {
                return '';
            }
            $messageAll = (new model\Message())->all($messageBoardOne['page']);
            foreach ($messageAll as $key => $value) {
                $messageAll[$key] = $this->listItem($value);
            }
            return $messageAll->items() ? json_encode($messageAll->items()) : '';
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function add()
    {
        if (Request::isAjax()) {
            $messageAdd = (new model\Message())->add();
            if (is_numeric($messageAdd)) {
                return $messageAdd > 0 ? showTip('留言提交成功，管理员审核后即可显示在留言区！') : showTip('留言失败！', 0);
            } else {
                return showTip($messageAdd, 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $Text = new model\Text();
        $item['content'] = htmlBrNbsp($Text->content($item['text_id_content']));
        $item['reply'] = htmlBrNbsp($Text->content($item['text_id_reply']));
        $item['date'] = dateFormat($item['date'], 'Y-m-d');
        return $item;
    }
}
