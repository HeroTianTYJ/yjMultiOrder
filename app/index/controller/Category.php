<?php

namespace app\index\controller;

use app\index\library\Tool;
use app\index\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use yjrj\Wechat;

class Category extends Base
{
    public function index()
    {
        $Category = new model\Category();
        if (Request::param('id', 0) != 0) {
            $categoryOne = $Category->one();
            if (!$categoryOne) {
                return $this->failed('不存在此品牌分类！');
            }
        } else {
            $categoryOne = $Category->one2();
            if (!$categoryOne) {
                $categoryOne = $Category->one3();
                if (!$categoryOne) {
                    return $this->failed('暂无品牌分类！');
                }
            }
        }

        Tool::click($categoryOne['id'], 'category');

        $categoryPageOne = (new model\CategoryPage())->one();
        $categoryPageOne['id'] = $categoryOne['id'];

        if (
            Config::get('system.wechat_app_id') && Config::get('system.wechat_app_secret') &&
            in_array(device(), ['harmonyWechat', 'androidWechat', 'iphoneWechat', 'windowsWechat', 'macWechat'])
        ) {
            $Wechat = new Wechat([
                'app_id' => Config::get('system.wechat_app_id'),
                'app_secret' => Config::get('system.wechat_app_secret')
            ]);
            $categoryPageOne['share'] = $Wechat->getShareConfig();
        } else {
            $categoryPageOne['share'] = false;
        }

        $Text = new model\Text();
        $categoryPageOne['code'] = $Text->content($categoryPageOne['text_id_code']);
        $categoryPageOne['nav'] = $Text->content($categoryPageOne['text_id_nav']);

        $navStr = '';
        if ($categoryPageOne['nav']) {
            $nav = explode("\r\n", $categoryPageOne['nav']);
            $icon = explode("\r\n", $categoryPageOne['icon']);
            $navStr = '<nav class="nav"><ul>';
            foreach ($nav as $key => $value) {
                if (isset($icon[$key]) && strstr($icon[$key], '[img=')) {
                    preg_match('/\[img=(.*)]/U', $icon[$key], $img);
                    $value = str_replace(
                        '<strong>',
                        '<strong><span style="background:url(' . (Config::get('system.qiniu_domain') ?
                            Config::get('system.qiniu_domain') :
                            Config::get('url.web1') . Config::get('dir.upload')) . $img[1] .
                        ') no-repeat;"></span>',
                        $value
                    );
                } else {
                    $value = str_replace('<strong>', '<strong><span class="' . ($icon[$key] ?? '') .
                        '"></span>', $value);
                }
                $navStr .= '<li>' . $value . '</li>';
            }
            $navStr .= '</ul></nav>';
        }
        $categoryPageOne['nav_str'] = $navStr;

        foreach (
            [
                'name', 'title', 'keyword', 'description', 'copyright', 'code', 'nav_str', 'share_title', 'share_desc'
            ] as $value
        ) {
            $categoryPageOne[$value] = str_replace('{CategoryName}', $categoryOne['name'], $categoryPageOne[$value]);
        }

        $categoryPageOne['one_category'] = $Category->all();
        $categoryAll = $Category->all($categoryOne['id']);
        if ($categoryAll) {
            $Brand = new model\Brand();
            foreach ($categoryAll as $key => $value) {
                $categoryAll[$key]['brand'] = $Brand->all($value['id']);
            }
        }
        $categoryPageOne['two_category'] = $categoryAll;

        View::assign(['One' => $categoryPageOne]);
        return $this->view();
    }
}
