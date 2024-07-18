<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class CategoryPage extends Base
{
    private array $codeType = ['使用全局代码 + 独立代码', '仅使用独立代码'];

    public function index()
    {
        $categoryPageAll = (new model\CategoryPage())->all();
        if (Request::isAjax()) {
            foreach ($categoryPageAll as $key => $value) {
                $categoryPageAll[$key]['url'] = Config::get('url.web1') . Config::get('system.index_php') .
                    'category.html';
            }
            return $categoryPageAll->items() ? json_encode($categoryPageAll->items()) : '';
        }
        View::assign(['Total' => $categoryPageAll->total()]);
        return $this->view();
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $CategoryPage = new model\CategoryPage();
            $categoryPageOne = $CategoryPage->one();
            if (!$categoryPageOne) {
                return showTip('不存在此品牌分类页！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo')) {
                    return showTip('演示站，品牌分类页无法修改！', 0);
                }
                $categoryPageModify = $CategoryPage
                    ->modify($categoryPageOne['text_id_code'], $categoryPageOne['text_id_nav']);
                return is_numeric($categoryPageModify) ?
                    showTip(['msg' => '品牌分类页修改成功！', 'data' => $CategoryPage->one()]) :
                    showTip($categoryPageModify, 0);
            }
            Html::codeType($this->codeType, $categoryPageOne['code_type']);
            $Text = new model\Text();
            $categoryPageOne['code'] = $Text->content($categoryPageOne['text_id_code']);
            $categoryPageOne['nav'] = $Text->content($categoryPageOne['text_id_nav']);
            View::assign(['One' => $categoryPageOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }
}
