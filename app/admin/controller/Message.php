<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Request;
use think\facade\View;
use yjrj\QQWry;

class Message extends Base
{
    private array $isView = [['green', '否'], ['red', '是']];

    public function index()
    {
        $messageAll = (new model\Message())->all();
        if (Request::isAjax()) {
            foreach ($messageAll as $key => $value) {
                $messageAll[$key] = $this->listItem($value);
            }
            return $messageAll->items() ? json_encode($messageAll->items()) : '';
        }
        View::assign(['Total' => $messageAll->total()]);
        Html::messageBoard(Request::get('message_board_id'));
        Html::isView($this->isView, Request::get('is_view', -1));
        return $this->view();
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Message = new model\Message();
            $messageOne = $Message->one();
            if (!$messageOne) {
                return showTip('不存在此留言！', 0);
            }
            if (Request::get('action') == 'do') {
                $messageModify = $Message->modify($messageOne['text_id_content'], $messageOne['text_id_reply']);
                return is_numeric($messageModify) ?
                    showTip(['msg' => '留言修改成功！', 'data' => $this->listItem($Message->one())]) :
                    showTip($messageModify, 0);
            }
            $Text = new model\Text();
            $messageOne['content'] = $Text->content($messageOne['text_id_content']);
            $messageOne['reply'] = $Text->content($messageOne['text_id_reply']);
            View::assign(['One' => $messageOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isView()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Message = new model\Message();
            $messageOne = $Message->one();
            if (!$messageOne) {
                return showTip('不存在此留言！', 0);
            }
            if ($messageOne['is_view'] == 0) {
                return $Message->isView(1) ? showTip('设置留言精选成功！') : showTip('设置留言精选失败！', 0);
            } else {
                return $Message->isView(0) ? showTip('取消留言精选成功！') : showTip('取消留言精选失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Message = new model\Message();
            $textId = [];
            if (Request::post('id')) {
                $messageOne = $Message->one();
                if (!$messageOne) {
                    return showTip('不存在此留言！', 0);
                }
                $textId[] = $messageOne['text_id_content'];
                $textId[] = $messageOne['text_id_reply'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $messageOne = $Message->one($value);
                    if (!$messageOne) {
                        return showTip('不存在您勾选的留言！', 0);
                    }
                    $textId[] = $messageOne['text_id_content'];
                    $textId[] = $messageOne['text_id_reply'];
                }
            }
            if ($Message->remove()) {
                (new model\Text())->remove($textId);
                return showTip('留言删除成功！');
            } else {
                return showTip('留言删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $item['tel'] = keyword($item['tel']);
        $item['email'] = keyword($item['email']);
        $Text = new model\Text();
        $item['content'] = keyword(htmlBrNbsp($Text->content($item['text_id_content'])));
        $item['ip'] = keyword($item['ip']) . ' ' . QQWry::getAddress($item['ip']);
        $messageBoardOne = (new model\MessageBoard())->one($item['message_board_id']);
        $item['message_board'] = $messageBoardOne ? $messageBoardOne['name'] : '此留言板已被删除';
        $item['reply'] = keyword(htmlBrNbsp($Text->content($item['text_id_reply'])));
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
