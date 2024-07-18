<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class BrandPage extends Base
{
    private array $codeType = ['使用全局代码 + 独立代码', '仅使用独立代码'];

    public function index()
    {
        $brandPageAll = (new model\BrandPage())->all();
        if (Request::isAjax()) {
            return $brandPageAll->items() ? json_encode($brandPageAll->items()) : '';
        }
        View::assign(['Total' => $brandPageAll->total()]);
        return $this->view();
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $BrandPage = new model\BrandPage();
            $brandPageOne = $BrandPage->one();
            if (!$brandPageOne) {
                return showTip('不存在此品牌详情页！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo')) {
                    return showTip('演示站，品牌详情页无法修改！', 0);
                }
                $brandPageModify = $BrandPage->modify($brandPageOne['text_id_code'], $brandPageOne['text_id_nav']);
                return is_numeric($brandPageModify) ?
                    showTip(['msg' => '品牌详情页修改成功！', 'data' => $BrandPage->one()]) :
                    showTip($brandPageModify, 0);
            }
            Html::codeType($this->codeType, $brandPageOne['code_type']);
            $Text = new model\Text();
            $brandPageOne['code'] = $Text->content($brandPageOne['text_id_code']);
            $brandPageOne['nav'] = $Text->content($brandPageOne['text_id_nav']);
            View::assign(['One' => $brandPageOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }
}
