<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\library\Json;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Template extends Base
{
    private array $type = ['商品页模板', '独立模板'];
    private array $template = ['手机版1', '手机版2', '手机版3', '手机版4', '电脑版'];
    private array $productType = ['单分类', '多分类'];
    private array $productViewType = ['单选按钮', '下拉框'];
    private array $isShowSearch = ['隐藏', '显示'];
    private array $isShowSend = ['隐藏', '显示'];
    private array $isSmsVerify = ['关闭', '开启'];
    private array $isSmsNotify = ['关闭', '开启'];

    public function index()
    {
        $templateAll = (new model\Template())->all();
        if (Request::isAjax()) {
            foreach ($templateAll as $key => $value) {
                $templateAll[$key] = $this->listItem($value);
            }
            return $templateAll->items() ? json_encode($templateAll->items()) : '';
        }
        View::assign(['Total' => $templateAll->total()]);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $templateAdd = (new model\Template())->add();
                if (is_numeric($templateAdd)) {
                    return $templateAdd > 0 ? showTip('模板添加成功！') : showTip('模板添加失败！', 0);
                } else {
                    return showTip($templateAdd, 0);
                }
            }
            Html::typeRadio($this->type);
            Html::template($this->template);
            Html::templateStyle();
            Html::productType($this->productType);
            Html::productSort();
            Html::productViewType($this->productViewType);
            Html::field();
            Html::payment();
            Html::isShowSearch($this->isShowSearch);
            Html::isShowSend($this->isShowSend);
            Html::captcha();
            Html::isSmsVerify($this->isSmsVerify);
            Html::isSmsNotify($this->isSmsNotify);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Template = new model\Template();
            $templateOne = $Template->one();
            if (!$templateOne) {
                return showTip('不存在此模板！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo') && Request::post('id') <= 5) {
                    return showTip('演示站，id<=5的模板无法修改！', 0);
                }
                $templateModify = $Template->modify();
                return is_numeric($templateModify) ?
                    showTip(['msg' => '模板修改成功！', 'data' => $this->listItem($Template->one())]) :
                    showTip($templateModify, 0);
            }
            Html::typeRadio($this->type, $templateOne['type']);
            Html::template($this->template, $templateOne['template']);
            Html::templateStyle($templateOne['template_style_id']);
            Html::productType($this->productType, $templateOne['product_type']);
            Html::productSort($templateOne['product_sort_ids']);
            Html::productViewType($this->productViewType, $templateOne['product_view_type']);
            Html::field($templateOne['field_ids']);
            Html::payment($templateOne['payment_ids'], $templateOne['payment_default']);
            Html::isShowSearch($this->isShowSearch, $templateOne['is_show_search']);
            Html::isShowSend($this->isShowSend, $templateOne['is_show_send']);
            Html::captcha($templateOne['captcha_id']);
            Html::isSmsVerify($this->isSmsVerify, $templateOne['is_sms_verify']);
            Html::isSmsNotify($this->isSmsNotify, $templateOne['is_sms_notify']);
            View::assign(['One' => $templateOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function code()
    {
        if (Request::isAjax() && Request::post('id')) {
            $templateOne = (new model\Template())->one();
            if (!$templateOne) {
                return showTip('不存在此模板！', 0);
            }
            if ($templateOne['type'] == 0) {
                return showTip('此模板为商品页模板，无法获取代码！', 0);
            }
            View::assign(['One' => $templateOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function templateView()
    {
        return (new \app\common\controller\Template())->html(Request::get('id'), [], 1);
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，模板无法删除！', 0);
            }
            $Template = new model\Template();
            if (Request::post('id')) {
                if (!$Template->one()) {
                    return showTip('不存在此模板！', 0);
                }
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    if (!$Template->one($value)) {
                        return showTip('不存在您勾选的模板！', 0);
                    }
                }
            }
            return $Template->remove() ? showTip('模板删除成功！') : showTip('模板删除失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function ajaxProduct()
    {
        if (Request::isAjax()) {
            return Json::product(Request::post('product_ids1'), Request::post('product_sort_id'));
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function ajaxProduct2()
    {
        if (Request::isAjax()) {
            return Json::product2(Request::post('product_ids2'));
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isDefault()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Template = new model\Template();
            if (!$Template->one()) {
                return showTip('不存在此模板！', 0);
            }
            return $Template->isDefault() ? showTip('设置默认模板成功！') : showTip('设置默认模板失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['type_str'] = $this->type[$item['type']];
        $item['name'] = keyword($item['name']);
        $item['template'] = $this->template[$item['template']];
        $item['is_show_search'] = $this->isShowSearch[$item['is_show_search']];
        $item['is_show_send'] = $this->isShowSend[$item['is_show_send']];
        if ($item['captcha_id']) {
            $captcha = Config::get('captcha');
            $item['captcha'] = isset($captcha[$item['captcha_id']]) ?
                $captcha[$item['captcha_id']]['name'] : '此验证码已被删除';
        } else {
            $item['captcha'] = '不添加';
        }
        $item['is_sms_verify'] = $this->isSmsVerify[$item['is_sms_verify']];
        $item['is_sms_notify'] = $this->isSmsNotify[$item['is_sms_notify']];
        $item['date'] = dateFormat($item['date']);
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'order/' . $item['id'] . '.html';
        return $item;
    }
}
