<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class MessageBoard extends Base
{
    private array $field = ['姓名', '联系电话', '电子邮箱', '留言内容'];

    public function index()
    {
        $messageBoardAll = (new model\MessageBoard())->all();
        if (Request::isAjax()) {
            foreach ($messageBoardAll as $key => $value) {
                $messageBoardAll[$key] = $this->listItem($value);
            }
            return $messageBoardAll->items() ? json_encode($messageBoardAll->items()) : '';
        }
        View::assign(['Total' => $messageBoardAll->total()]);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $messageBoardAdd = (new model\MessageBoard())->add();
                if (is_numeric($messageBoardAdd)) {
                    return $messageBoardAdd > 0 ? showTip('留言板添加成功！') : showTip('留言板添加失败！', 0);
                } else {
                    return showTip($messageBoardAdd, 0);
                }
            }
            Html::messageField($this->field);
            Html::captcha();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $MessageBoard = new model\MessageBoard();
            $messageBoardOne = $MessageBoard->one();
            if (!$messageBoardOne) {
                return showTip('不存在此留言板！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo') && Request::post('id') == 1) {
                    return showTip('演示站，id为1的留言板无法修改！', 0);
                }
                $messageBoardModify = $MessageBoard->modify();
                return is_numeric($messageBoardModify) ?
                    showTip(['msg' => '留言板修改成功！', 'data' => $this->listItem($MessageBoard->one())]) :
                    showTip($messageBoardModify, 0);
            }
            Html::messageField($this->field, explode(',', $messageBoardOne['field']));
            Html::captcha($messageBoardOne['captcha_id']);
            View::assign(['One' => $messageBoardOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $MessageBoard = new model\MessageBoard();
            if (Request::post('id')) {
                if (!$MessageBoard->one()) {
                    return showTip('不存在此留言板！', 0);
                }
                if (Config::get('app.demo') && Request::post('id') == 1) {
                    return showTip('演示站，id为1的留言板无法删除！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$MessageBoard->one($value)) {
                        return showTip('不存在您勾选的留言板！', 0);
                    }
                    if (Config::get('app.demo') && $value == 1) {
                        return showTip('演示站，id为1的留言板无法删除！', 0);
                    }
                }
            }
            return $MessageBoard->remove() ? showTip('留言板删除成功！') : showTip('留言板删除失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        if ($item['field'] != '') {
            $field = explode(',', $item['field']);
            $item['field'] = '';
            foreach ($field as $v) {
                $item['field'] .= $this->field[$v] . '、';
            }
            $item['field'] = substr($item['field'], 0, -3);
        }
        $captcha = Config::get('captcha');
        if ($item['captcha_id']) {
            $item['captcha'] = isset($captcha[$item['captcha_id']]) ?
                $captcha[$item['captcha_id']]['name'] : '此验证码已被删除';
        } else {
            $item['captcha'] = '不添加';
        }
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
